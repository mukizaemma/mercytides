<?php

namespace App\Models;

use App\Support\MercyTidesContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FounderStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'tagline',
        'header_caption',
        'founder_name',
        'founder_role',
        'content',
        'founder_image',
    ];

    public static function firstOrEmpty(): self
    {
        if (! Schema::hasTable((new static())->getTable())) {
            return new static();
        }

        return static::query()->first() ?? new static();
    }

    public static function firstOrSingleton(): self
    {
        if (! Schema::hasTable((new static())->getTable())) {
            return new static(self::defaultAttributes());
        }

        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create(self::defaultAttributes());
    }

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        $meta = MercyTidesContent::foundingStoryMeta();

        return [
            'title' => $meta['title'],
            'tagline' => $meta['tagline'],
            'header_caption' => $meta['header_caption'],
            'founder_name' => $meta['founder_name'],
            'founder_role' => $meta['founder_role'],
            'content' => MercyTidesContent::foundingStoryHtml(),
        ];
    }
}
