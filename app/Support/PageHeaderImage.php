<?php

namespace App\Support;

use App\Models\Background;
use App\Models\PageHeader;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\Schema;

class PageHeaderImage
{
    public static function resolve(?string $pageKey = null, ?string $explicitImage = null): ?string
    {
        if (! empty($explicitImage)) {
            return $explicitImage;
        }

        if ($pageKey) {
            $pageHeader = PageHeader::findByKey($pageKey);
            $url = $pageHeader?->imageUrl();
            if ($url) {
                return $url;
            }
        }

        $default = PageHeader::defaultHeader();
        if ($default?->imageUrl()) {
            return $default->imageUrl();
        }

        return self::legacyFallback();
    }

    public static function forDonate(?int $sponsorId = null): ?string
    {
        if ($sponsorId && Schema::hasTable('sponsorships')) {
            $mother = Sponsorship::query()->find($sponsorId);
            $url = self::sponsorshipUrl($mother);
            if ($url) {
                return $url;
            }
        }

        return self::resolve('donate');
    }

    public static function forSponsorListing(): ?string
    {
        return self::resolve('sponsor_young_mother');
    }

    public static function sponsorshipUrl(?Sponsorship $sponsorship): ?string
    {
        if (! $sponsorship || empty($sponsorship->image)) {
            return null;
        }

        $path = ltrim($sponsorship->image, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_contains($path, '/')) {
            return asset('storage/' . $path);
        }

        return asset('storage/images/sponsorship/' . $path);
    }

    public static function defaultMercyTidesHero(): ?string
    {
        return self::resolve(PageHeader::DEFAULT_KEY);
    }

    protected static function legacyFallback(): ?string
    {
        $setting = Setting::query()->first();
        if (! empty($setting?->page_header_image)) {
            $path = ltrim((string) $setting->page_header_image, '/');
            if (str_contains($path, '/')) {
                return asset('storage/' . $path);
            }

            return asset('storage/images' . $setting->page_header_image);
        }

        $slide = Slide::query()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->oldest()
            ->first();

        if ($slide) {
            $url = Slide::publicImageUrl($slide->image);

            return $url !== '' ? $url : null;
        }

        $program = Program::query()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->oldest()
            ->first();

        if ($program) {
            return $program->coverImageUrl();
        }

        $about = Background::firstOrEmpty();
        foreach (['image2', 'image1', 'image'] as $field) {
            if (! empty($about->{$field})) {
                return asset('storage/images/' . ltrim($about->{$field}, '/'));
            }
        }

        return null;
    }
}
