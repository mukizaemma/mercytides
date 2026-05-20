<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $table= "programs";
    protected $fillable = [
        'title',
        'description',
        'slug',
        'image',
        'added_by',
    ];

    public function activities(){
        return $this->hasMany(Activity::class);
    }

    public function images(){
        return $this->hasMany(Programimage::class);
    }

    public function coverImageUrl(): ?string
    {
        return static::resolveCoverImageUrl($this->image);
    }

    public static function resolveCoverImageUrl(?string $image): ?string
    {
        if (empty($image)) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $path = ltrim($image, '/');

        if (str_contains($path, '/')) {
            return asset('storage/' . $path);
        }

        return asset('storage/images/programs/' . $path);
    }
}
