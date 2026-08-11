<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'images';

    protected $fillable = [
        'program_id',
        'caption',
        'image',
        'youtube_url',
        'show_on_gallery',
        'sort_order',
    ];

    protected $casts = [
        'show_on_gallery' => 'boolean',
        'sort_order' => 'integer',
        'program_id' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function scopeOnGallery(Builder $query): Builder
    {
        static::ensureGalleryColumns();

        return $query->where('show_on_gallery', true);
    }

    public function scopeHiddenFromGallery(Builder $query): Builder
    {
        static::ensureGalleryColumns();

        return $query->where('show_on_gallery', false);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        static::ensureGalleryColumns();

        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * Add visibility/sort columns when the live database has not been migrated yet.
     */
    public static function ensureGalleryColumns(): void
    {
        static $ready = false;

        if ($ready || ! Schema::hasTable('images')) {
            return;
        }

        $after = Schema::hasColumn('images', 'youtube_url') ? 'youtube_url' : 'image';

        if (! Schema::hasColumn('images', 'show_on_gallery')) {
            Schema::table('images', function (Blueprint $table) use ($after) {
                $table->boolean('show_on_gallery')->default(true)->after($after);
            });
        }

        if (! Schema::hasColumn('images', 'sort_order')) {
            Schema::table('images', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('show_on_gallery');
            });
        }

        $ready = true;
    }

    public static function normalizePath(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', trim($path)), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return $path;
    }

    /**
     * Ensure an uploaded file is represented in the gallery catalog.
     */
    public static function registerFromPath(string $path, array $attributes = []): ?self
    {
        static::ensureGalleryColumns();

        $path = static::normalizePath($path);
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        $basename = basename($path);
        $existing = static::withTrashed()
            ->where(function (Builder $query) use ($path, $basename) {
                $query->where('image', $path)
                    ->orWhere('image', '/'.$path)
                    ->orWhere('image', $basename);
            })
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->show_on_gallery = false;
            }
            if (empty($existing->image) || $existing->image === $basename) {
                $existing->image = $path;
            }
            foreach (['caption', 'program_id', 'youtube_url'] as $key) {
                if (array_key_exists($key, $attributes) && $attributes[$key] !== null && $attributes[$key] !== '') {
                    $existing->{$key} = $attributes[$key];
                }
            }
            $existing->save();

            return $existing;
        }

        $row = new static();
        $row->image = $path;
        $row->caption = $attributes['caption'] ?? null;
        $row->program_id = $attributes['program_id'] ?? null;
        $row->youtube_url = $attributes['youtube_url'] ?? null;
        $row->show_on_gallery = array_key_exists('show_on_gallery', $attributes)
            ? (bool) $attributes['show_on_gallery']
            : true;
        $row->sort_order = 0;
        if (! empty($attributes['created_at'])) {
            $row->created_at = $attributes['created_at'];
        }
        $row->save();

        return $row;
    }

    /**
     * Pull every image already on disk into the gallery catalog.
     */
    public static function syncUploadedImages(): int
    {
        static::ensureGalleryColumns();

        $disk = Storage::disk('public');
        if (! $disk->exists('images')) {
            return 0;
        }

        $added = 0;
        foreach ($disk->allFiles('images') as $path) {
            if (! preg_match('/\.(jpe?g|png|gif|webp)$/i', $path)) {
                continue;
            }

            $basename = basename($path);
            $exists = static::withTrashed()
                ->where(function (Builder $query) use ($path, $basename) {
                    $query->where('image', $path)
                        ->orWhere('image', '/'.$path)
                        ->orWhere('image', $basename);
                })
                ->exists();

            $mtime = Carbon::createFromTimestamp($disk->lastModified($path));
            $row = $exists
                ? static::registerFromPath($path)
                : static::registerFromPath($path, ['created_at' => $mtime]);

            if ($row && $row->created_at && $row->created_at->gt($mtime)) {
                $row->timestamps = false;
                $row->created_at = $mtime;
                $row->save();
            }

            if (! $exists) {
                $added++;
            }
        }

        return $added;
    }

    public function sourceLabel(): string
    {
        if ($this->isVideo() && empty($this->image)) {
            return 'YouTube';
        }

        $dir = strtolower(basename(dirname((string) $this->image)));
        $labels = [
            'gallery' => 'Gallery',
            'staff' => 'Leadership',
            'news' => 'Updates',
            'programs' => 'Programs',
            'projects' => 'Stories',
            'events' => 'Events',
            'campaigns' => 'Campaigns',
            'testimonies' => 'Testimonials',
            'sponsorship' => 'Mothers',
            'partners' => 'Partners',
            'slides' => 'Home slides',
            'products' => 'Shop',
            'founder' => 'Founder',
            'page-headers' => 'Page headers',
            'impacts' => 'Impact',
            'students' => 'Students',
        ];

        if (isset($labels[$dir])) {
            return $labels[$dir];
        }

        return $dir !== '' && $dir !== '.' ? Str::headline($dir) : 'Upload';
    }

    public function isVideo(): bool
    {
        return $this->youtubeId() !== null;
    }

    public function youtubeId(): ?string
    {
        $url = trim((string) ($this->youtube_url ?? ''));
        if ($url === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function youtubeEmbedUrl(): ?string
    {
        $id = $this->youtubeId();

        return $id ? 'https://www.youtube.com/embed/'.$id.'?autoplay=1&rel=0' : null;
    }

    public function youtubeWatchUrl(): ?string
    {
        $id = $this->youtubeId();

        return $id ? 'https://www.youtube.com/watch?v='.$id : null;
    }

    /**
     * Public URL for the grid thumbnail (uploaded image or YouTube poster).
     */
    public function thumbUrl(): string
    {
        if (! empty($this->image)) {
            $path = ltrim((string) $this->image, '/');
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            if (str_starts_with($path, 'storage/')) {
                return asset($path);
            }

            return asset('storage/'.$path);
        }

        $id = $this->youtubeId();
        if ($id) {
            return 'https://img.youtube.com/vi/'.$id.'/hqdefault.jpg';
        }

        return asset('assets/img/breadcrumb/breadcrumb-shape-1.png');
    }

    /**
     * Full-size media source for the lightbox (image URL or embed URL).
     */
    public function lightboxSrc(): string
    {
        if ($this->isVideo()) {
            return (string) $this->youtubeEmbedUrl();
        }

        return $this->thumbUrl();
    }
}
