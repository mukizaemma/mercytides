<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'donation_payment_methods')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('donation_payment_methods')->nullable()->after('recaptcha_secret_key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'donation_payment_methods')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('donation_payment_methods');
            });
        }
    }
};
