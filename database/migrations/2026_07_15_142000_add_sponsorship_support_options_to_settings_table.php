<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'sponsorship_support_options')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('sponsorship_support_options')->nullable()->after('donation_payment_methods');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'sponsorship_support_options')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('sponsorship_support_options');
            });
        }
    }
};
