<?php

use App\Models\PageHeader;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_headers')) {
            Schema::create('page_headers', function (Blueprint $table) {
                $table->id();
                $table->string('page_key', 64)->unique();
                $table->string('label');
                $table->string('image')->nullable();
                $table->boolean('is_default')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        PageHeader::ensureCatalog();

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'page_header_image')) {
            $setting = Setting::query()->first();
            $legacy = trim((string) ($setting?->page_header_image ?? ''));
            if ($legacy !== '') {
                $default = PageHeader::query()->where('page_key', PageHeader::DEFAULT_KEY)->first();
                if ($default && empty($default->image)) {
                    $path = ltrim($legacy, '/');
                    $default->image = str_contains($path, '/')
                        ? $path
                        : 'images/'.$path;
                    $default->is_default = true;
                    $default->save();
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_headers');
    }
};
