<?php

namespace App\Models;

use App\Support\MercyTidesContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'position',
        'slug',
        'category',
        'facebook',
        'instagram',
        'twitter',
        'display',
        'status',
    ];

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
