<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mothers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->longText('description')->nullable();
            $table->longText('vision')->nullable();
            $table->string('image');
            $table->string('slug')->nullable()->unique();
            $table->string('status')->default('Published');
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mothers');
    }
};
