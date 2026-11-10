<?php

namespace App\Support;

use App\Models\SiteCopy;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Admin-editable static copy. Empty or missing values fall back to the catalog defaults
 * so the public site keeps its current wording until an admin saves a change.
 */
class SiteContent
{
    /** @var array<string, string>|null */
    private static ?array $stored = null;

    public static function text(string $key, ?string $fallback = null): string
    {
        $fallback ??= SiteCopyCatalog::default($key);
        $raw = self::raw($key);
        if ($raw === null || trim($raw) === '') {
            return $fallback;
        }

        return trim($raw);
    }

    public static function html(string $key, ?string $fallback = null): string
    {
        $fallback ??= SiteCopyCatalog::default($key);
        $raw = self::raw($key);
        if ($raw === null) {
            return $fallback;
        }

        $plain = trim(str_replace("\xc2\xa0", ' ', strip_tags(html_entity_decode($raw))));
        if ($plain === '') {
            return $fallback;
        }

        return $raw;
    }

    /** @return list<string> */
    public static function lines(string $key, ?array $fallback = null): array
    {
        $fallback ??= self::splitLines(SiteCopyCatalog::default($key));
        $raw = self::raw($key);
        if ($raw === null || trim($raw) === '') {
            return $fallback;
        }

        $lines = self::splitLines($raw);

        return $lines === [] ? $fallback : $lines;
    }

    public static function linesText(string $key): string
    {
        $raw = self::raw($key);
        if ($raw === null || trim($raw) === '') {
            return SiteCopyCatalog::default($key);
        }

        return $raw;
    }

    public static function fill(string $key, array $replacements, ?string $fallback = null): string
    {
        return strtr(self::text($key, $fallback), $replacements);
    }

    /**
     * @return list<array{icon: string, text: string}>
     */
    public static function objectives(): array
    {
        $decoded = json_decode((string) self::raw('objectives.items'), true);
        $items = self::normalizeObjectives(is_array($decoded) ? $decoded : []);

        return $items !== [] ? $items : MercyTidesContent::objectiveBlueprint();
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{icon: string, text: string}>
     */
    public static function normalizeObjectives(array $rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $text = trim((string) ($row['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $items[] = [
                'icon' => self::fullIcon((string) ($row['icon'] ?? ''), 'fas fa-check-circle'),
                'text' => $text,
            ];
        }

        return $items;
    }

    public static function fullIcon(string $value, string $fallback): string
    {
        $value = trim($value);
        if (preg_match('/^fa-[a-z0-9-]+$/i', $value)) {
            $value = 'fas '.$value;
        }
        if (! preg_match('/^(fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i', $value)) {
            return $fallback;
        }

        return $value;
    }

    public static function faIcon(string $value, string $fallback): string
    {
        $value = trim(preg_replace('/^(fas|far|fab|fal|fad)\s+/i', '', trim($value)) ?? '');
        if (! preg_match('/^fa-[a-z0-9-]+$/i', $value)) {
            $fallback = trim(preg_replace('/^(fas|far|fab|fal|fad)\s+/i', '', trim($fallback)) ?? '');

            return preg_match('/^fa-[a-z0-9-]+$/i', $fallback) ? $fallback : 'fa-heart';
        }

        return $value;
    }

    public static function raw(string $key): ?string
    {
        $stored = self::stored();

        return array_key_exists($key, $stored) ? (string) $stored[$key] : null;
    }

    /** @param  array<string, string|null>  $pairs */
    public static function putMany(array $pairs): void
    {
        if (! Schema::hasTable('site_copies')) {
            return;
        }

        foreach ($pairs as $key => $value) {
            SiteCopy::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        self::$stored = null;
    }

    public static function tableReady(): bool
    {
        try {
            if (Schema::hasTable('site_copies')) {
                return true;
            }

            Schema::create('site_copies', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });

            return true;
        } catch (Throwable $e) {
            try {
                return Schema::hasTable('site_copies');
            } catch (Throwable $ignored) {
                return false;
            }
        }
    }

    /** @return array<string, string> */
    private static function stored(): array
    {
        if (self::$stored !== null) {
            return self::$stored;
        }

        if (! Schema::hasTable('site_copies')) {
            return self::$stored = [];
        }

        return self::$stored = SiteCopy::query()->pluck('value', 'key')->all();
    }

    /** @return list<string> */
    private static function splitLines(string $value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));
    }
}
