<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Sponsorship extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sponsorships';

    protected $fillable = [
        'type',
        'slug',
        'names',
        'age',
        'sex',
        'status',
        'show_status_publicly',
        'publish_status',
        'phone',
        'contact',
        'address',
        'testimany',
        'challenges',
        'vision',
        'video_url',
        'video_path',
        'monthly_need',
        'image',
        'added_by',
    ];

    protected $casts = [
        'show_status_publicly' => 'boolean',
    ];

    public function donations()
    {
        return $this->hasMany(Donate::class, 'sponsorship_id');
    }

    public static function publicImageUrl(?string $image): string
    {
        if (empty($image)) {
            return '';
        }

        if (str_contains($image, '/')) {
            return asset('storage/' . ltrim($image, '/'));
        }

        return asset('storage/images/sponsorship/' . ltrim($image, '/'));
    }

    public function displayName(): string
    {
        $name = trim((string) ($this->names ?? ''));

        return $name !== '' ? $name : 'Sponsorship profile';
    }

    public function profileRoute(): string
    {
        $key = $this->slug ?: $this->id;

        return route('sponsorshipProfile', ['slug' => $key]);
    }

    public function isPublished(): bool
    {
        $status = strtolower((string) ($this->publish_status ?? 'published'));

        return in_array($status, ['published', 'publish', 'active'], true);
    }

    public function isAvailable(): bool
    {
        return strtolower((string) ($this->status ?? '')) !== 'sponsored';
    }

    public function shouldShowStatusPublicly(): bool
    {
        return (bool) ($this->show_status_publicly ?? false);
    }

    public function hasProfileDetails(): bool
    {
        return collect([
            $this->names,
            $this->testimany,
            $this->challenges,
            $this->vision,
        ])->contains(fn ($value) => trim(strip_tags((string) $value)) !== '');
    }

    public function typeLabel(): string
    {
        return \App\Support\MercyTidesContent::sponsorshipTypeLabel((string) ($this->type ?? 'child'));
    }

    public static function publicVideoUrl(?string $video): string
    {
        if (empty($video)) {
            return '';
        }

        if (str_contains($video, '/')) {
            return asset('storage/' . ltrim($video, '/'));
        }

        return asset('storage/videos/sponsorship/' . ltrim($video, '/'));
    }

    public function hasUploadedVideo(): bool
    {
        return trim((string) ($this->video_path ?? '')) !== '';
    }

    public function hasStoryVideo(): bool
    {
        return $this->hasUploadedVideo() || $this->youtubeEmbedUrl() !== null;
    }

    public function uploadedVideoUrl(): ?string
    {
        if (! $this->hasUploadedVideo()) {
            return null;
        }

        $url = self::publicVideoUrl($this->video_path);

        return $url !== '' ? $url : null;
    }

    public function youtubeEmbedUrl(): ?string
    {
        $url = trim((string) ($this->video_url ?? ''));
        if ($url === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('publish_status')
                ->orWhereIn('publish_status', ['Published', 'Publish', 'Active']);
        });
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    protected static function booted(): void
    {
        static::saving(function (Sponsorship $profile) {
            if (! empty($profile->names) && empty($profile->slug)) {
                $base = Str::slug($profile->names);
                $slug = $base !== '' ? $base : 'sponsor-' . ($profile->id ?? 'new');
                $suffix = 1;

                while (
                    static::query()
                        ->where('slug', $slug)
                        ->when($profile->exists, fn ($q) => $q->where('id', '!=', $profile->id))
                        ->exists()
                ) {
                    $slug = $base . '-' . $suffix;
                    $suffix++;
                }

                $profile->slug = $slug;
            }
        });
    }
}
