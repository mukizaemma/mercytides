<?php

namespace App\Http;

use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    /**
     * New upload wins; otherwise reuse a library path (no duplicate file).
     */
    protected function imageFromRequest(
        Request $request,
        string $fileKey,
        string $directory,
        array $options = []
    ): ?string {
        $libraryKey = $options['library_key'] ?? $this->libraryKeyFor($fileKey);

        if ($request->hasFile($fileKey) && ! is_array($request->file($fileKey))) {
            return $request->file($fileKey)->storeOptimized($directory, 'public', $options);
        }

        return $this->resolveLibraryPath($request->input($libraryKey));
    }

    /**
     * @return list<string>
     */
    protected function galleryFromRequest(
        Request $request,
        string $fileKey,
        string $directory,
        array $options = []
    ): array {
        $libraryKey = $options['library_key'] ?? $this->libraryKeyFor($fileKey, true);
        $paths = [];

        if ($request->hasFile($fileKey)) {
            foreach ((array) $request->file($fileKey) as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $paths[] = $file->storeOptimized($directory, 'public', $options);
                }
            }
        }

        foreach ((array) $request->input($libraryKey, []) as $raw) {
            $path = $this->resolveLibraryPath($raw);
            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @return array<string, mixed>
     */
    protected function imageInputRules(string $fileKey, bool $required = false): array
    {
        $libraryKey = $this->libraryKeyFor($fileKey);
        $fileRule = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'];

        if ($required) {
            $fileRule[] = function (string $attribute, mixed $value, \Closure $fail) use ($fileKey, $libraryKey): void {
                $request = request();
                if (! $request->hasFile($fileKey) && ! filled($request->input($libraryKey))) {
                    $fail('Please upload a new image or choose an existing one from the library.');
                }
            };
        }

        return [
            $fileKey => $fileRule,
            $libraryKey => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function resolveLibraryPath(mixed $raw): ?string
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $raw), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (! str_starts_with($path, 'images/')) {
            return null;
        }
        if (str_contains($path, '..')) {
            return null;
        }
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        try {
            \App\Models\Image::registerFromPath($path);
        } catch (\Throwable) {
            // Gallery catalog is best-effort.
        }

        return $path;
    }

    protected function libraryKeyFor(string $fileKey, bool $multiple = false): string
    {
        $base = rtrim($fileKey, '[]');

        return $multiple ? $base.'_paths' : $base.'_path';
    }
}
