<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mother extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'description',
        'vision',
        'image',
        'slug',
        'status',
        'added_by',
    ];

    public static function publicImageUrl(?string $image): string
    {
        if (empty($image)) {
            return '';
        }

        if (str_contains($image, '/')) {
            return asset('storage/' . ltrim($image, '/'));
        }

        return asset('storage/images/mothers/' . ltrim($image, '/'));
    }

    public function hasProfileDetails(): bool
    {
        return collect([
            $this->name,
            $this->description,
            $this->vision,
        ])->contains(fn ($value) => trim(strip_tags((string) $value)) !== '');
    }

    public function displayName(): string
    {
        $name = trim((string) ($this->name ?? ''));

        return $name !== '' ? $name : 'Young mother';
    }

    public function profileRoute(): string
    {
        $key = $this->slug ?: $this->id;

        return route('motherProfile', ['slug' => $key]);
    }

    protected static function booted(): void
    {
        static::saving(function (Mother $mother) {
            if (! empty($mother->slug)) {
                return;
            }

            $base = Str::slug((string) ($mother->name ?? ''));
            if ($base === '') {
                $base = 'mother-'.($mother->id ?: 'new');
            }

            $slug = $base;
            $suffix = 1;

            while (
                static::query()
                    ->where('slug', $slug)
                    ->when($mother->exists, fn ($q) => $q->where('id', '!=', $mother->id))
                    ->exists()
            ) {
                $slug = $base.'-'.$suffix;
                $suffix++;
            }

            $mother->slug = $slug;
        });
    }
}
