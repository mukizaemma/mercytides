<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsorships', 'show_status_publicly')) {
                $table->boolean('show_status_publicly')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'show_status_publicly')) {
                $table->dropColumn('show_status_publicly');
            }
        });
    }
};
