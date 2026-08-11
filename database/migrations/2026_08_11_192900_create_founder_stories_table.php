<?php

use App\Models\FounderStory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('founder_stories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('tagline')->nullable();
            $table->string('header_caption')->nullable();
            $table->string('founder_name')->nullable();
            $table->string('founder_role')->nullable();
            $table->longText('content')->nullable();
            $table->string('founder_image')->nullable();
            $table->timestamps();
        });

        FounderStory::firstOrSingleton();
    }

    public function down(): void
    {
        Schema::dropIfExists('founder_stories');
    }
};
