<?php

namespace Wisdech\Uploader;

use Illuminate\Support\ServiceProvider;

class UploaderProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'uploader-migrations');
    }
}
