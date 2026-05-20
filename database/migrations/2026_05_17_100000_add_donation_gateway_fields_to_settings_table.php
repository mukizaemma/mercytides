<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'donation_gateway_enabled')) {
                $table->boolean('donation_gateway_enabled')->default(false)->after('donation_payment_methods');
            }
            if (! Schema::hasColumn('settings', 'donation_stripe_publishable_key')) {
                $table->string('donation_stripe_publishable_key', 255)->nullable()->after('donation_gateway_enabled');
            }
            if (! Schema::hasColumn('settings', 'donation_stripe_secret_key')) {
                $table->string('donation_stripe_secret_key', 255)->nullable()->after('donation_stripe_publishable_key');
            }
            if (! Schema::hasColumn('settings', 'donation_default_currency')) {
                $table->string('donation_default_currency', 8)->default('USD')->after('donation_stripe_secret_key');
            }
            if (! Schema::hasColumn('settings', 'donation_gateway_notice')) {
                $table->text('donation_gateway_notice')->nullable()->after('donation_default_currency');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            foreach ([
                'donation_gateway_enabled',
                'donation_stripe_publishable_key',
                'donation_stripe_secret_key',
                'donation_default_currency',
                'donation_gateway_notice',
            ] as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
