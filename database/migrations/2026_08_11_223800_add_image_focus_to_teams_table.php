<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('image_focus', 32)->nullable()->after('image');
        });

        // Nudge known off-center portraits into the circle (can be overridden in admin).
        DB::table('teams')->where('names', 'like', '%Margaret%')->update(['image_focus' => '80 28']);
        DB::table('teams')->where('names', 'like', '%Jonathan%')->update(['image_focus' => '50 30']);
        DB::table('teams')->where('names', 'like', '%Samuel%')->update(['image_focus' => '48 18']);
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('image_focus');
        });
    }
};
