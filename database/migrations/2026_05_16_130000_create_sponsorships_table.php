<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sponsorships')) {
            return;
        }

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->string('names');
            $table->string('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('status')->default('Not Sponsored');
            $table->string('phone')->nullable();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->text('testimany')->nullable();
            $table->string('monthly_need')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
