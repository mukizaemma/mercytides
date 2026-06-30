<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ImageUploadService
{
    public function store(UploadedFile $file, string $directory, string $disk = 'public', array $options = []): string
    {
        $directory = trim($directory, '/');
        $filename = $this->buildFilename($file, $options['extension'] ?? null);
        $path = $directory === '' ? $filename : $directory.'/'.$filename;

        $binary = $this->optimize($file, $options);
        Storage::disk($disk)->put($path, $binary);

        return $path;
    }

    public function storeAs(UploadedFile $file, string $directory, string $filename, string $disk = 'public', array $options = []): string
    {
        $directory = trim($directory, '/');
        $filename = $this->normalizeFilename($filename, $options['extension'] ?? null);
        $path = $directory === '' ? $filename : $directory.'/'.$filename;

        $binary = $this->optimize($file, $options);
        Storage::disk($disk)->put($path, $binary);

        return $path;
    }

    /**
     * Legacy helpers used by older controllers that only persist the basename.
     */
    public function storeBasename(UploadedFile $file, string $directory, string $disk = 'public', array $options = []): string
    {
        return basename($this->store($file, $directory, $disk, $options));
    }

    public function storeAsBasename(UploadedFile $file, string $directory, string $filename, string $disk = 'public', array $options = []): string
    {
        return basename($this->storeAs($file, $directory, $filename, $disk, $options));
    }

    public function optimize(UploadedFile $file, array $options = []): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Invalid upload.');
        }

        $mime = strtolower((string) $file->getMimeType());
        if (! str_starts_with($mime, 'image/')) {
            throw new InvalidArgumentException('Only image uploads can be optimized.');
        }

        if (! extension_loaded('gd')) {
            return (string) file_get_contents($file->getRealPath());
        }

        $settings = $this->resolveSettings($options);

        if ($file->getSize() <= $settings['max_bytes']) {
            $dimensions = @getimagesize($file->getRealPath());
            if (
                is_array($dimensions)
                && ($dimensions[0] ?? 0) <= $settings['max_width']
                && ($dimensions[1] ?? 0) <= $settings['max_height']
            ) {
                return (string) file_get_contents($file->getRealPath());
            }
        }

        $source = $this->readImage($file->getRealPath(), $mime);
        if ($source === null) {
            return (string) file_get_contents($file->getRealPath());
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $working = $this->resizeIfNeeded($source, $width, $height, $settings['max_width'], $settings['max_height']);

        if ($working !== $source) {
            imagedestroy($source);
        }

        $hasAlpha = $this->hasTransparency($working);
        $preferJpeg = ! $hasAlpha && in_array($mime, ['image/jpeg', 'image/jpg', 'image/webp'], true);

        $binary = $this->encodeWithTargetSize(
            $working,
            $preferJpeg,
            $hasAlpha,
            $settings['max_bytes'],
            $settings['initial_quality'],
            $settings['min_quality'],
            $settings['png_compression']
        );

        imagedestroy($working);

        if ($binary === null) {
            return (string) file_get_contents($file->getRealPath());
        }

        return $binary;
    }

    private function resolveSettings(array $options): array
    {
        $preset = [];
        if (! empty($options['preset']) && is_array(config('image.presets.'.$options['preset']))) {
            $preset = config('image.presets.'.$options['preset']);
        }

        return [
            'max_bytes' => (int) ($options['max_bytes'] ?? $preset['max_bytes'] ?? config('image.max_bytes')),
            'max_width' => (int) ($options['max_width'] ?? $preset['max_width'] ?? config('image.max_width')),
            'max_height' => (int) ($options['max_height'] ?? $preset['max_height'] ?? config('image.max_height')),
            'initial_quality' => (int) ($options['initial_quality'] ?? config('image.initial_quality')),
            'min_quality' => (int) ($options['min_quality'] ?? config('image.min_quality')),
            'png_compression' => (int) ($options['png_compression'] ?? config('image.png_compression')),
        ];
    }

    private function buildFilename(UploadedFile $file, ?string $forcedExtension = null): string
    {
        $extension = $forcedExtension ?: $this->preferredExtension($file);

        return Str::uuid()->toString().'.'.$extension;
    }

    private function normalizeFilename(string $filename, ?string $forcedExtension = null): string
    {
        $basename = basename($filename);
        if ($forcedExtension) {
            $stem = pathinfo($basename, PATHINFO_FILENAME);

            return $stem.'.'.$forcedExtension;
        }

        return $basename;
    }

    private function preferredExtension(UploadedFile $file): string
    {
        $mime = strtolower((string) $file->getMimeType());

        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => function_exists('imagewebp') ? 'webp' : 'jpg',
            'image/gif' => 'jpg',
            default => 'jpg',
        };
    }

    private function readImage(string $path, string $mime): ?\GdImage
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path) ?: null,
            'image/png' => @imagecreatefrompng($path) ?: null,
            'image/webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            'image/gif' => @imagecreatefromgif($path) ?: null,
            default => null,
        };
    }

    private function resizeIfNeeded(\GdImage $image, int $width, int $height, int $maxWidth, int $maxHeight): \GdImage
    {
        $scale = min(1, $maxWidth / max($width, 1), $maxHeight / max($height, 1));
        if ($scale >= 1) {
            return $image;
        }

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $canvas;
    }

    private function hasTransparency(\GdImage $image): bool
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $stepX = max(1, (int) floor($width / 12));
        $stepY = max(1, (int) floor($height / 12));

        for ($x = 0; $x < $width; $x += $stepX) {
            for ($y = 0; $y < $height; $y += $stepY) {
                $rgba = imagecolorat($image, $x, $y);
                if (($rgba & 0x7F000000) >> 24 > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function encodeWithTargetSize(
        \GdImage $image,
        bool $preferJpeg,
        bool $hasAlpha,
        int $maxBytes,
        int $initialQuality,
        int $minQuality,
        int $pngCompression
    ): ?string {
        $attempts = 0;
        $quality = $initialQuality;
        $scale = 1.0;
        $working = $image;

        while ($attempts < 14) {
            $binary = $this->encodeImage($working, $preferJpeg, $hasAlpha, $quality, $pngCompression);

            if ($binary !== null && strlen($binary) <= $maxBytes) {
                if ($working !== $image) {
                    imagedestroy($working);
                }

                return $binary;
            }

            if ($quality > $minQuality) {
                $quality = max($minQuality, $quality - 7);
                $attempts++;
                continue;
            }

            if ($scale <= 0.55) {
                break;
            }

            $scale *= 0.88;
            $next = $this->downscaleCopy($working, $scale);
            if ($working !== $image) {
                imagedestroy($working);
            }
            $working = $next;
            $quality = $initialQuality;
            $attempts++;
        }

        $fallback = $this->encodeImage($working, $preferJpeg, $hasAlpha, $minQuality, $pngCompression);
        if ($working !== $image) {
            imagedestroy($working);
        }

        return $fallback;
    }

    private function downscaleCopy(\GdImage $image, float $scale): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $canvas;
    }

    private function encodeImage(
        \GdImage $image,
        bool $preferJpeg,
        bool $hasAlpha,
        int $quality,
        int $pngCompression
    ): ?string {
        ob_start();

        $saved = false;
        if ($preferJpeg && ! $hasAlpha) {
            $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, imagesx($image), imagesy($image), $white);
            imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            $saved = imagejpeg($canvas, null, $quality);
            imagedestroy($canvas);
        } elseif ($hasAlpha) {
            $saved = imagepng($image, null, $pngCompression);
        } elseif (function_exists('imagewebp')) {
            $saved = imagewebp($image, null, $quality);
        } else {
            $saved = imagejpeg($image, null, $quality);
        }

        $binary = ob_get_clean();

        return $saved ? $binary : null;
    }
}
