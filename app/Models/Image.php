<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
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
