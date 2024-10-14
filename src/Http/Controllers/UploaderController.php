<?php

namespace Wisdech\Uploader\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Wisdech\Uploader\Models\Media;

class UploaderController
{
    public function store(Request $request)
    {
        $file = null;
        $path = Carbon::today()->format('Y/m');

        if ($request->hasFile('file')) {
            $path = "public/file/{$path}/";
            $file = $request->file('file');
        }

        if ($request->hasFile('image')) {
            $path = "public/image/{$path}/";
            $file = $request->file('image');
        }

        if ($file != null) {
            $file = $this->storeRequestFile($path, $file);
            return new JsonResponse(['success' => 1, 'file' => $file]);
        }

        if ($request->has('url')) {
            $file = $this->storeRequestUrl($request->string('url'));
            return new JsonResponse(['success' => 1, 'file' => $file]);
        }

        return new JsonResponse(['success' => 0, 'file' => null]);
    }

    private function storeRequestUrl(string $url)
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $tempPath = "temp/" . time() . ".$extension";

        Storage::put($tempPath, file_get_contents($url));

        $hash = File::hash(storage_path("app/$tempPath"));
        $path = "public/image/" . Carbon::today()->format('Y/m');
        $savePath = "$path/$hash.$extension";

        if (Storage::exists($savePath)) {
            $file = Media::findByHash($hash)->getEditorContent();
        } else {
            Storage::move($tempPath, $savePath);

            $file = Media::create([
                'name' => 'NONAME',
                'hash' => $hash,
                'path' => $savePath,
                'extension' => $extension,
            ])->getEditorContent();
        }

        return $file;
    }

    private function storeRequestFile(string $path, $file)
    {
        $hash = File::hash($file);
        $name = $hash . '.' . $file->extension();

        if (Storage::exists($path . $name)) {
            $file = Media::findByHash($hash)->getEditorContent();
        } else {
            $path = Storage::putFileAs($path, $file, $name);

            $file = Media::create([
                'size' => $file->getSize(),
                'name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'extension' => strtolower($file->getClientOriginalExtension()),
                'hash' => $hash,
                'path' => $path,
            ])->getEditorContent();
        }

        return $file;
    }
}
