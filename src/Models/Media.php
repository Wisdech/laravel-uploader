<?php

namespace Wisdech\Uploader\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name',
        'hash',
        'path',
        'size',
        'extension',
        'mime_type',
        'used',
    ];

    protected $appends = ['url'];

    protected $casts = [
        'used' => 'boolean',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::url($this->path)
        );
    }

    public static function findByHash($hash): ?Media
    {
        return self::where('hash', $hash)->first();
    }

    public function getEditorContent(): array
    {
        return [
            'url' => Storage::url($this->path),
            'hash' => $this->hash,
            'size' => $this->size,
            'name' => $this->name,
            'title' => $this->name,
            'type' => $this->getFileType(),
            'extension' => $this->extension,
        ];
    }

    public function getFileType(): string
    {

        if (str_starts_with($this->mime_type, 'image')) {
            return 'IMAGE';
        }

        if (str_starts_with($this->mime_type, 'audio')) {
            return 'AUDIO';
        }

        if (str_starts_with($this->mime_type, 'video')) {
            return 'VIDEO';
        }

        if ($this->extension == 'pdf') {
            return 'PDF';
        }

        if ($this->extension == 'txt') {
            return 'TXT';
        }

        if (in_array($this->extension, ['doc', 'docx'])) {
            return 'WORD';
        }

        if (in_array($this->extension, ['ppt', 'pptx', 'keynote'])) {
            return 'SLIDE';
        }

        if (in_array($this->extension, ['xls', 'xlsx', 'csv'])) {
            return 'SHEET';
        }

        if (in_array($this->extension, ['zip', 'rar', '7z', 'tz', 'gz', 'tar'])) {
            return 'ZIP';
        }

        return 'UNKNOWN';
    }
}
