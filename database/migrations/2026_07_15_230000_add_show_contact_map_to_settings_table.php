<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'show_contact_map')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('show_contact_map')->default(false)->after('google_map_embed_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'show_contact_map')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('show_contact_map');
            });
        }
    }
};
