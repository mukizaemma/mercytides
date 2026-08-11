<?php

namespace App\Models;

use App\Support\MercyTidesContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'teams';

    protected $fillable = [
        'names',
        'email',
        'phone',
        'bio',
        'image',
        'image_focus',
        'position',
        'slug',
        'category',
        'facebook',
        'instagram',
        'twitter',
        'display',
        'status',
    ];

    public const DEFAULT_IMAGE_FOCUS = '50 18';

    /**
     * Add image_focus when the live database has not been migrated yet.
     */
    public static function ensureImageFocusColumn(): bool
    {
        static $ready = null;

        if ($ready !== null) {
            return $ready;
        }

        if (! Schema::hasTable('teams')) {
            return $ready = false;
        }

        if (! Schema::hasColumn('teams', 'image_focus')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->string('image_focus', 32)->nullable()->after('image');
            });

            static::query()->where('names', 'like', '%Margaret%')->update(['image_focus' => '80 28']);
            static::query()->where('names', 'like', '%Jonathan%')->update(['image_focus' => '50 30']);
            static::query()->where('names', 'like', '%Samuel%')->update(['image_focus' => '48 18']);
        }

        return $ready = true;
    }

    /**
     * CSS object-position value that keeps faces inside the circular crop.
     */
    public function imageFocusCss(): string
    {
        $parsed = static::parseImageFocus($this->image_focus);

        return $parsed['x'].'% '.$parsed['y'].'%';
    }

    /**
     * @return array{x: float, y: float}
     */
    public static function parseImageFocus(?string $value): array
    {
        $value = trim((string) $value);
        if (preg_match('/^(\d{1,3}(?:\.\d+)?)\s*%?\s+[x,]?\s*(\d{1,3}(?:\.\d+)?)\s*%?$/i', $value, $m)) {
            return [
                'x' => max(0, min(100, (float) $m[1])),
                'y' => max(0, min(100, (float) $m[2])),
            ];
        }

        [$x, $y] = array_map('floatval', explode(' ', self::DEFAULT_IMAGE_FOCUS));

        return ['x' => $x, 'y' => $y];
    }

    public static function normalizeImageFocus(?string $value): string
    {
        $parsed = static::parseImageFocus($value);

        return rtrim(rtrim(number_format($parsed['x'], 1, '.', ''), '0'), '.').' '
            .rtrim(rtrim(number_format($parsed['y'], 1, '.', ''), '0'), '.');
    }

    /**
     * Restore trashed leaders and ensure the default Magambo profiles exist.
     */
    public static function ensureLeadershipSeeded(): void
    {
        static::onlyTrashed()->restore();

        foreach (MercyTidesContent::leadershipTeam() as $leader) {
            $slug = Str::slug($leader['names']);
            $member = static::withTrashed()->updateOrCreate(
                ['slug' => $slug],
                [
                    'names' => $leader['names'],
                    'position' => $leader['position'],
                    'bio' => $leader['bio'],
                    'category' => 'Administration',
                    'display' => 'Yes',
                    'status' => 'Active',
                ]
            );

            if ($member->trashed()) {
                $member->restore();
            }
        }
    }
}
