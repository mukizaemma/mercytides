<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            if (! Schema::hasColumn('images', 'show_on_gallery')) {
                $table->boolean('show_on_gallery')->default(true)->after('youtube_url');
            }
            if (! Schema::hasColumn('images', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('show_on_gallery');
            }
        });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            if (Schema::hasColumn('images', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('images', 'show_on_gallery')) {
                $table->dropColumn('show_on_gallery');
            }
        });
    }
};
