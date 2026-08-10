<?php

namespace App\Http\Controllers;

use App\Models\Blogimages;
use App\Models\Image;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{
    /**
     * JSON list of reusable uploaded images for the admin media picker.
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $items = collect();

        if (Schema::hasTable('images')) {
            Image::query()
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->latest()
                ->limit(120)
                ->get(['id', 'image', 'caption'])
                ->each(function (Image $row) use ($items) {
                    $path = $this->normalizePath((string) $row->image);
                    if ($path === null) {
                        return;
                    }
                    $items->push([
                        'path' => $path,
                        'url' => asset('storage/'.$path),
                        'label' => $row->caption ?: basename($path),
                        'source' => 'gallery',
                    ]);
                });
        }

        if (Schema::hasTable('news')) {
            News::query()
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->latest()
                ->limit(80)
                ->get(['id', 'image', 'title'])
                ->each(function (News $row) use ($items) {
                    $path = $this->normalizePath((string) $row->image, 'images/news');
                    if ($path === null) {
                        return;
                    }
                    $items->push([
                        'path' => $path,
                        'url' => asset('storage/'.$path),
                        'label' => $row->title ?: basename($path),
                        'source' => 'updates',
                    ]);
                });
        }

        if (Schema::hasTable('blogimages')) {
            Blogimages::query()
                ->whereNotNull('gallery')
                ->where('gallery', '!=', '')
                ->latest()
                ->limit(120)
                ->get(['id', 'gallery', 'caption'])
                ->each(function (Blogimages $row) use ($items) {
                    $path = $this->normalizePath((string) $row->gallery);
                    if ($path === null) {
                        return;
                    }
                    $items->push([
                        'path' => $path,
                        'url' => asset('storage/'.$path),
                        'label' => $row->caption ?: basename($path),
                        'source' => 'updates',
                    ]);
                });
        }

        $unique = $items
            ->unique('path')
            ->values()
            ->filter(function (array $item) use ($q) {
                if ($q === '') {
                    return true;
                }
                $hay = strtolower($item['label'].' '.$item['path'].' '.$item['source']);

                return str_contains($hay, strtolower($q));
            })
            ->take(160)
            ->values();

        return response()->json([
            'data' => $unique,
        ]);
    }

    /**
     * Normalize a stored path and ensure the file exists on the public disk.
     */
    private function normalizePath(string $raw, ?string $fallbackDir = null): ?string
    {
        $path = ltrim(str_replace('\\', '/', $raw), '/');
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // Legacy basename-only values (e.g. events / older news).
        if ($fallbackDir && ! str_contains($path, '/')) {
            $path = rtrim($fallbackDir, '/').'/'.$path;
        }

        if (! str_starts_with($path, 'images/')) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return $path;
    }
}
