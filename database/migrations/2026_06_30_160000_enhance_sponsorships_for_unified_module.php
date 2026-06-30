<?php

use App\Models\Mother;
use App\Models\Sponsorship;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsorships', 'type')) {
                $table->string('type', 32)->default('child')->after('id');
            }
            if (! Schema::hasColumn('sponsorships', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('names');
            }
            if (! Schema::hasColumn('sponsorships', 'challenges')) {
                $table->longText('challenges')->nullable()->after('testimany');
            }
            if (! Schema::hasColumn('sponsorships', 'vision')) {
                $table->longText('vision')->nullable()->after('challenges');
            }
            if (! Schema::hasColumn('sponsorships', 'video_url')) {
                $table->string('video_url')->nullable()->after('vision');
            }
            if (! Schema::hasColumn('sponsorships', 'publish_status')) {
                $table->string('publish_status', 32)->default('Published')->after('status');
            }
            if (! Schema::hasColumn('sponsorships', 'added_by')) {
                $table->unsignedBigInteger('added_by')->nullable()->after('image');
            }
        });

        if (! Schema::hasTable('mothers')) {
            return;
        }

        Mother::query()->orderBy('id')->each(function (Mother $mother) {
            $slug = $mother->slug;
            if (empty($slug)) {
                $base = Str::slug((string) $mother->name) ?: 'mother-' . $mother->id;
                $slug = $base;
                $suffix = 1;
                while (Sponsorship::query()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $suffix;
                    $suffix++;
                }
            }

            if (Sponsorship::query()->where('slug', $slug)->exists()) {
                return;
            }

            Sponsorship::query()->create([
                'type' => 'young_mother',
                'slug' => $slug,
                'names' => trim((string) $mother->name) !== '' ? $mother->name : 'Young mother #' . $mother->id,
                'age' => $mother->age,
                'testimany' => $mother->description,
                'vision' => $mother->vision,
                'image' => $mother->image,
                'status' => 'Not Sponsored',
                'publish_status' => $mother->status ?: 'Published',
                'added_by' => $mother->added_by ?? null,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            foreach (['type', 'slug', 'challenges', 'vision', 'video_url', 'publish_status', 'added_by'] as $column) {
                if (Schema::hasColumn('sponsorships', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
