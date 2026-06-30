<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('backgrounds')) {
            return;
        }

        Schema::table('backgrounds', function (Blueprint $table) {
            if (! Schema::hasColumn('backgrounds', 'get_involved_intro')) {
                $table->longText('get_involved_intro')->nullable()->after('donations');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('backgrounds')) {
            return;
        }

        Schema::table('backgrounds', function (Blueprint $table) {
            if (Schema::hasColumn('backgrounds', 'get_involved_intro')) {
                $table->dropColumn('get_involved_intro');
            }
        });
    }
};
