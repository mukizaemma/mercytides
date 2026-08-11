<?php

namespace App\Support;

class StorageImage
{
    /**
     * Public URL for a stored image path (full storage path or legacy basename).
     */
    public static function url(?string $path, ?string $legacyDir = null): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $raw = trim($path);
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $normalized = ltrim(str_replace('\\', '/', $raw), '/');
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'images/')) {
            return asset('storage/'.$normalized);
        }

        if ($legacyDir) {
            $dir = trim(str_replace('\\', '/', $legacyDir), '/');

            return asset('storage/'.$dir.'/'.$normalized);
        }

        return asset('storage/images/'.$normalized);
    }
}
