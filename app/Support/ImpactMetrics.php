<?php

namespace App\Support;

use App\Models\Impact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ImpactMetrics
{
    /**
     * Active impact stats for public pages (label + value).
     * Falls back to defaults when no Impact Items exist yet.
     *
     * @return Collection<int, array{label: string, value: string}>
     */
    public static function forPublic(?int $limit = null): Collection
    {
        $items = collect();

        if (Schema::hasTable('impacts')) {
            $query = Impact::query()->active()->ordered();
            if ($limit !== null) {
                $query->limit($limit);
            }

            $items = $query->get()
                ->filter(fn (Impact $item) => trim((string) ($item->value ?? '')) !== '')
                ->map(fn (Impact $item) => [
                    'label' => (string) ($item->title ?? ''),
                    'value' => (string) $item->value,
                ])
                ->values();
        }

        if ($items->isNotEmpty()) {
            return $items;
        }

        return collect([
            ['label' => 'Mothers empowered', 'value' => '6'],
            ['label' => 'Communities', 'value' => '1'],
            ['label' => 'Christ-centered care', 'value' => '100%'],
        ]);
    }

    /**
     * Bootstrap column class for a responsive stats grid.
     */
    public static function columnClass(int $count): string
    {
        return match (true) {
            $count <= 1 => 'col-12 col-md-6',
            $count === 2 => 'col-6 col-md-6',
            $count === 3 => 'col-6 col-md-4',
            default => 'col-6 col-md-3',
        };
    }
}
