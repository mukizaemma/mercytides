<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'child_info')) {
                $table->text('child_info')->nullable()->after('status');
            }
            if (! Schema::hasColumn('members', 'challenge')) {
                $table->text('challenge')->nullable()->after('child_info');
            }
            if (! Schema::hasColumn('members', 'document')) {
                $table->string('document')->nullable()->after('challenge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $columns = array_filter(['child_info', 'challenge', 'document'], fn ($c) => Schema::hasColumn('members', $c));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
