<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('form_type', 64)->index();
            $table->string('channel', 16)->index();
            $table->string('submitter_name')->nullable();
            $table->string('submitter_email')->nullable()->index();
            $table->string('submitter_phone', 64)->nullable();
            $table->json('payload');
            $table->text('message_preview')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'recaptcha_site_key')) {
                    $table->string('recaptcha_site_key')->nullable()->after('email');
                }
                if (! Schema::hasColumn('settings', 'recaptcha_secret_key')) {
                    $table->string('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (Schema::hasColumn('settings', 'recaptcha_secret_key')) {
                    $table->dropColumn('recaptcha_secret_key');
                }
                if (Schema::hasColumn('settings', 'recaptcha_site_key')) {
                    $table->dropColumn('recaptcha_site_key');
                }
            });
        }
    }
};
