<?php

namespace App\Http;

use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

trait StoresOptimizedImages
{
    protected function storeOptimizedImage(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        array $options = []
    ): string {
        return app(ImageUploadService::class)->store($file, $directory, $disk, $options);
    }

    protected function storeOptimizedImageAs(
        UploadedFile $file,
        string $directory,
        string $filename,
        string $disk = 'public',
        array $options = []
    ): string {
        return app(ImageUploadService::class)->storeAs($file, $directory, $filename, $disk, $options);
    }

    protected function storeOptimizedImageBasename(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        array $options = []
    ): string {
        return app(ImageUploadService::class)->storeBasename($file, $directory, $disk, $options);
    }
}
