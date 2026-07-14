<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PageHeader extends Model
{
    use HasFactory;

    public const DEFAULT_KEY = 'default';

    protected $table = 'page_headers';

    protected $fillable = [
        'page_key',
        'label',
        'image',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Catalog of public pages that use the shared breadcrumb/header.
     *
     * @return array<string, array{label: string, sort: int}>
     */
    public static function catalog(): array
    {
        return [
            self::DEFAULT_KEY => ['label' => 'Site default (fallback)', 'sort' => 0],
            'about' => ['label' => 'About / Our Story', 'sort' => 10],
            'mission' => ['label' => 'Vision & Mission', 'sort' => 20],
            'approach' => ['label' => 'Programs & Core Values', 'sort' => 30],
            'model' => ['label' => 'Where We Work', 'sort' => 40],
            'team' => ['label' => 'Foundation Leadership', 'sort' => 50],
            'programs' => ['label' => 'Our Programs', 'sort' => 60],
            'impact' => ['label' => 'Our Impact', 'sort' => 70],
            'services' => ['label' => 'Our Services', 'sort' => 80],
            'factory' => ['label' => 'Our Factory', 'sort' => 90],
            'products' => ['label' => 'Our Products', 'sort' => 100],
            'events' => ['label' => 'Upcoming Events', 'sort' => 110],
            'blog' => ['label' => 'News & Updates', 'sort' => 120],
            'testimonials' => ['label' => 'Testimonials', 'sort' => 130],
            'gallery_mothers' => ['label' => 'Young Mothers Gallery', 'sort' => 140],
            'sponsorship_hub' => ['label' => 'Sponsorship hub', 'sort' => 150],
            'sponsor_child' => ['label' => 'Sponsor a Child', 'sort' => 160],
            'sponsor_young_mother' => ['label' => 'Sponsor a young mother', 'sort' => 170],
            'sponsor_family' => ['label' => 'Sponsor a family', 'sort' => 180],
            'sponsorship_profile' => ['label' => 'Sponsorship profile detail', 'sort' => 190],
            'mother_profile' => ['label' => 'Mother profile detail', 'sort' => 200],
            'donate' => ['label' => 'Donate', 'sort' => 210],
            'get_involved' => ['label' => 'Get Involved', 'sort' => 220],
            'volunteer' => ['label' => 'Volunteer', 'sort' => 230],
            'apply_for_support' => ['label' => 'Apply for Support', 'sort' => 240],
            'contact' => ['label' => 'Contact', 'sort' => 250],
            'request_order' => ['label' => 'Request an order', 'sort' => 260],
        ];
    }

    public static function ensureCatalog(): void
    {
        if (! Schema::hasTable('page_headers')) {
            return;
        }

        foreach (self::catalog() as $key => $meta) {
            self::query()->firstOrCreate(
                ['page_key' => $key],
                [
                    'label' => $meta['label'],
                    'sort_order' => $meta['sort'],
                    'is_default' => $key === self::DEFAULT_KEY,
                ]
            );
        }

        if (! self::query()->where('is_default', true)->exists()) {
            self::query()->where('page_key', self::DEFAULT_KEY)->update(['is_default' => true]);
        }
    }

    public static function publicImageUrl(?string $image): string
    {
        if (empty($image)) {
            return '';
        }

        $path = ltrim($image, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    public function imageUrl(): ?string
    {
        $url = self::publicImageUrl($this->image);

        return $url !== '' ? $url : null;
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public static function findByKey(?string $pageKey): ?self
    {
        if ($pageKey === null || $pageKey === '') {
            return null;
        }

        if (! Schema::hasTable('page_headers')) {
            return null;
        }

        return self::query()->where('page_key', $pageKey)->first();
    }

    public static function defaultHeader(): ?self
    {
        if (! Schema::hasTable('page_headers')) {
            return null;
        }

        return self::query()
            ->where('is_default', true)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->first()
            ?: self::query()
                ->where('page_key', self::DEFAULT_KEY)
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->first();
    }
}
