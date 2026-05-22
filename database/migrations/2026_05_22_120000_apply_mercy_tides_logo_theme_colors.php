<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $row = DB::table('settings')->first();
        if (! $row) {
            return;
        }

        $updates = [];

        if (Schema::hasColumn('settings', 'primary_color')) {
            $current = strtolower((string) ($row->primary_color ?? ''));
            if ($current === '' || in_array($current, ['#fad200', '#ffe135', '#ffeb3b'], true)) {
                $updates['primary_color'] = '#FFC107';
            }
        }

        if (Schema::hasColumn('settings', 'secondary_color')) {
            $current = strtolower((string) ($row->secondary_color ?? ''));
            if ($current === '' || in_array($current, ['#2c2c2c', '#1a1a1a', '#333333'], true)) {
                $updates['secondary_color'] = '#2E7D32';
            }
        }

        if (Schema::hasColumn('settings', 'neutral_color')) {
            $current = strtolower((string) ($row->neutral_color ?? ''));
            if ($current === '' || in_array($current, ['#b0b0b0', '#03a9f4'], true)) {
                $updates['neutral_color'] = '#0288D1';
            }
        }

        if ($updates !== []) {
            DB::table('settings')->where('id', $row->id)->update($updates);
        }
    }

    public function down(): void
    {
        // Non-destructive: leave customized colors as-is.
    }
};
