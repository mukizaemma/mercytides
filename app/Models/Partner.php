<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';

    protected $fillable = [
        'names',
        'slug',
        'description',
        'image',
        'facebook',
        'instagram',
        'twitter',
        'website',
    ];

    public static function publicImageUrl(?string $image): string
    {
        if (empty($image)) {
            return '';
        }

        $image = trim($image);
        if ($image === '') {
            return '';
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $path = ltrim($image, '/');

        // Full storage-relative path already (e.g. images/partners/foo.png)
        if (str_contains($path, '/')) {
            return asset('storage/' . $path);
        }

        return asset('storage/images/partners/' . $path);
    }

    public function logoUrl(): string
    {
        return self::publicImageUrl($this->image);
    }

    public function hasLogo(): bool
    {
        return $this->logoUrl() !== '';
    }
}
