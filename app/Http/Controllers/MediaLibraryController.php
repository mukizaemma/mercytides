<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{
    /**
     * JSON list of reusable uploaded images for the admin media picker.
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $disk = Storage::disk('public');
        $items = collect();

        if ($disk->exists('images')) {
            foreach ($disk->allFiles('images') as $path) {
                if (! preg_match('/\.(jpe?g|png|gif|webp)$/i', $path)) {
                    continue;
                }
                $items->push([
                    'path' => $path,
                    'url' => asset('storage/'.$path),
                    'label' => basename($path),
                    'source' => dirname($path),
                    'modified' => $disk->lastModified($path),
                ]);
            }
        }

        $unique = $items
            ->unique('path')
            ->sortByDesc('modified')
            ->values()
            ->filter(function (array $item) use ($q) {
                if ($q === '') {
                    return true;
                }
                $hay = strtolower($item['label'].' '.$item['path'].' '.$item['source']);

                return str_contains($hay, strtolower($q));
            })
            ->take(240)
            ->map(function (array $item) {
                unset($item['modified']);

                return $item;
            })
            ->values();

        return response()->json([
            'data' => $unique,
        ]);
    }
}
