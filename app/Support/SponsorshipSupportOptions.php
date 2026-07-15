<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SponsorshipSupportOptions
{
    /**
     * Default ways to support young mothers / sponsorship profiles.
     *
     * @return list<array{key: string, icon: string, label: string, text: string, sort: int, active: bool}>
     */
    public static function defaults(): array
    {
        return [
            [
                'key' => 'vocational',
                'icon' => 'fa-tools',
                'label' => 'Vocational Training',
                'text' => 'Help her gain practical skills for lasting income.',
                'sort' => 10,
                'active' => true,
            ],
            [
                'key' => 'transport',
                'icon' => 'fa-bus',
                'label' => 'Transport Facilitation',
                'text' => 'Cover travel so she can reach training, clinics, and work.',
                'sort' => 20,
                'active' => true,
            ],
            [
                'key' => 'food',
                'icon' => 'fa-shopping-basket',
                'label' => 'Food Packages',
                'text' => 'Provide nutritious food support for mother and child.',
                'sort' => 30,
                'active' => true,
            ],
            [
                'key' => 'health',
                'icon' => 'fa-heartbeat',
                'label' => 'Health insurance',
                'text' => 'Support healthcare coverage for mother and child.',
                'sort' => 40,
                'active' => true,
            ],
            [
                'key' => 'hygiene',
                'icon' => 'fa-soap',
                'label' => 'Hygiene and sanitation Materials',
                'text' => 'Supply hygiene kits and sanitation essentials.',
                'sort' => 50,
                'active' => true,
            ],
        ];
    }

    /**
     * All configured options (including inactive), sorted.
     *
     * @return list<array{key: string, icon: string, label: string, text: string, sort: int, active: bool}>
     */
    public static function all(): array
    {
        $stored = self::storedRaw();
        $options = is_array($stored) && $stored !== []
            ? array_values(array_filter(array_map([self::class, 'normalizeOption'], $stored)))
            : self::defaults();

        usort($options, static fn (array $a, array $b) => ($a['sort'] <=> $b['sort']) ?: strcmp($a['label'], $b['label']));

        return $options;
    }

    /**
     * Active options for public pages and forms.
     *
     * @return list<array{key: string, icon: string, label: string, text: string, sort: int, active: bool}>
     */
    public static function active(): array
    {
        return array_values(array_filter(self::all(), static fn (array $option) => $option['active']));
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::all() as $option) {
            $labels[$option['key']] = $option['label'];
        }

        return $labels;
    }

    /** @return array<string, string> */
    public static function activeLabels(): array
    {
        $labels = [];
        foreach (self::active() as $option) {
            $labels[$option['key']] = $option['label'];
        }

        return $labels;
    }

    public static function find(string $key): ?array
    {
        foreach (self::all() as $option) {
            if ($option['key'] === $key) {
                return $option;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $input
     * @return list<array{key: string, icon: string, label: string, text: string, sort: int, active: bool}>
     */
    public static function fromAdminInput(array $input): array
    {
        $options = [];
        $usedKeys = [];

        foreach ($input as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $key = trim((string) ($row['key'] ?? ''));
            if ($key === '') {
                $key = Str::slug($label);
            }
            $key = Str::slug(str_replace('_', '-', $key));
            $key = str_replace('-', '_', $key);
            if ($key === '') {
                $key = 'support_'.($index + 1);
            }

            $baseKey = $key;
            $suffix = 2;
            while (isset($usedKeys[$key])) {
                $key = $baseKey.'_'.$suffix;
                $suffix++;
            }
            $usedKeys[$key] = true;

            $icon = trim((string) ($row['icon'] ?? 'fa-heart'));
            if ($icon !== '' && ! str_starts_with($icon, 'fa-')) {
                $icon = 'fa-'.$icon;
            }

            $options[] = [
                'key' => $key,
                'icon' => $icon !== '' ? $icon : 'fa-heart',
                'label' => $label,
                'text' => trim((string) ($row['text'] ?? '')),
                'sort' => (int) ($row['sort'] ?? (($index + 1) * 10)),
                'active' => filter_var($row['active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        if ($options === []) {
            return self::defaults();
        }

        usort($options, static fn (array $a, array $b) => ($a['sort'] <=> $b['sort']) ?: strcmp($a['label'], $b['label']));

        return array_values($options);
    }

    /**
     * @param  mixed  $row
     * @return array{key: string, icon: string, label: string, text: string, sort: int, active: bool}|null
     */
    protected static function normalizeOption($row): ?array
    {
        if (! is_array($row)) {
            return null;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $key = trim((string) ($row['key'] ?? ''));
        if ($label === '' || $key === '') {
            return null;
        }

        $icon = trim((string) ($row['icon'] ?? 'fa-heart'));
        if ($icon !== '' && ! str_starts_with($icon, 'fa-')) {
            $icon = 'fa-'.$icon;
        }

        return [
            'key' => $key,
            'icon' => $icon !== '' ? $icon : 'fa-heart',
            'label' => $label,
            'text' => trim((string) ($row['text'] ?? '')),
            'sort' => (int) ($row['sort'] ?? 0),
            'active' => filter_var($row['active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    protected static function storedRaw(): mixed
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'sponsorship_support_options')) {
            return null;
        }

        return Setting::query()->value('sponsorship_support_options');
    }
}
