<?php

namespace App\Support;

use App\Models\Setting;

class DonationPaymentMethods
{
    public const CHANNEL_MOBILE_MONEY = 'mobile_money';

    public const CHANNEL_BANK = 'bank';

    public const CHANNEL_ONLINE = 'online';

    public const COMPLETION_MANUAL = 'manual';

    public const COMPLETION_EXTERNAL_LINK = 'external_link';

    public const COMPLETION_GATEWAY_UI = 'gateway_ui';

    public const COMPLETION_NOTIFY_ONLY = 'notify_only';

    /** @return list<array<string, mixed>> */
    public static function defaults(): array
    {
        return [
            [
                'id' => 'mtn_momo',
                'label' => 'MTN Mobile Money',
                'enabled' => true,
                'number' => '',
                'account_name' => '',
                'bank_name' => '',
                'branch' => '',
                'swift' => '',
                'payment_url' => '',
                'instructions' => 'Send to the number below. Use your full name as the reference.',
            ],
            [
                'id' => 'airtel_money',
                'label' => 'Airtel Money',
                'enabled' => true,
                'number' => '',
                'account_name' => '',
                'bank_name' => '',
                'branch' => '',
                'swift' => '',
                'payment_url' => '',
                'instructions' => 'For local donors in Uganda.',
            ],
            [
                'id' => 'bank_transfer',
                'label' => 'Bank transfer',
                'enabled' => false,
                'number' => '',
                'account_name' => '',
                'bank_name' => '',
                'branch' => '',
                'swift' => '',
                'payment_url' => '',
                'instructions' => '',
            ],
            [
                'id' => 'online_payment',
                'label' => 'Card / PayPal / Stripe',
                'enabled' => true,
                'number' => '',
                'account_name' => '',
                'bank_name' => '',
                'branch' => '',
                'swift' => '',
                'payment_url' => '',
                'instructions' => 'Pay securely with your card when online checkout is enabled.',
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function all(?Setting $setting = null): array
    {
        $setting ??= Setting::firstOrEmpty();
        $raw = $setting->donation_payment_methods ?? null;

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return static::mergeWithDefaults($decoded);
            }
        }

        if (is_array($raw)) {
            return static::mergeWithDefaults($raw);
        }

        return static::defaults();
    }

    /** @return list<array<string, mixed>> */
    public static function enabled(?Setting $setting = null): array
    {
        $setting ??= Setting::firstOrEmpty();

        return array_values(array_filter(static::all($setting), function (array $method) use ($setting) {
            if (empty($method['enabled'])) {
                return false;
            }

            return static::hasDisplayableDetails($method, $setting);
        }));
    }

    /** @return list<string> */
    public static function enabledIds(?Setting $setting = null): array
    {
        return array_map(fn (array $m) => (string) $m['id'], static::enabled($setting));
    }

    public static function find(string $id, ?Setting $setting = null): ?array
    {
        foreach (static::all($setting) as $method) {
            if (($method['id'] ?? '') === $id) {
                return $method;
            }
        }

        return null;
    }

    public static function label(string $id, ?Setting $setting = null): string
    {
        return static::find($id, $setting)['label'] ?? $id;
    }

    /**
     * @param  array<string, mixed>  $method
     */
    public static function channel(array $method): string
    {
        $id = (string) ($method['id'] ?? '');

        return match ($id) {
            'bank_transfer' => self::CHANNEL_BANK,
            'online_payment' => self::CHANNEL_ONLINE,
            default => self::CHANNEL_MOBILE_MONEY,
        };
    }

    /**
     * How the donor completes payment for this method.
     *
     * @param  array<string, mixed>  $method
     */
    public static function completionMode(array $method, ?Setting $setting = null): string
    {
        $setting ??= Setting::firstOrEmpty();
        $id = (string) ($method['id'] ?? '');

        if ($id === 'online_payment') {
            if (static::gatewayUiActive($setting)) {
                return self::COMPLETION_GATEWAY_UI;
            }
            if (trim((string) ($method['payment_url'] ?? '')) !== '') {
                return self::COMPLETION_EXTERNAL_LINK;
            }

            return self::COMPLETION_NOTIFY_ONLY;
        }

        return self::COMPLETION_MANUAL;
    }

    public static function gatewayUiActive(?Setting $setting = null): bool
    {
        $setting ??= Setting::firstOrEmpty();

        if (! ($setting->donation_gateway_enabled ?? false)) {
            return false;
        }

        return trim((string) ($setting->donation_stripe_publishable_key ?? '')) !== '';
    }

    /**
     * @return array{enabled: bool, publishable_key: string, currency: string, notice: string, ready: bool}
     */
    public static function gatewayConfig(?Setting $setting = null): array
    {
        $setting ??= Setting::firstOrEmpty();
        $enabled = static::gatewayUiActive($setting);
        $hasSecret = trim((string) ($setting->donation_stripe_secret_key ?? '')) !== '';

        return [
            'enabled' => $enabled,
            'publishable_key' => trim((string) ($setting->donation_stripe_publishable_key ?? '')),
            'currency' => strtoupper(trim((string) ($setting->donation_default_currency ?? 'USD')) ?: 'USD'),
            'notice' => trim((string) ($setting->donation_gateway_notice ?? ''))
                ?: 'Secure card checkout is being finalised. You can still notify our team below after giving.',
            'ready' => false,
            'has_secret_key' => $hasSecret,
        ];
    }

    /**
     * @param  array<string, mixed>  $method
     */
    public static function hasDisplayableDetails(array $method, ?Setting $setting = null): bool
    {
        $setting ??= Setting::firstOrEmpty();

        if (static::completionMode($method, $setting) === self::COMPLETION_GATEWAY_UI) {
            return true;
        }

        foreach (['number', 'account_name', 'bank_name', 'branch', 'swift', 'payment_url', 'instructions'] as $field) {
            if (trim((string) ($method[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $method
     * @return list<string>
     */
    public static function detailLines(array $method): array
    {
        $lines = [];

        if (! empty($method['number'])) {
            $label = static::channel($method) === self::CHANNEL_BANK ? 'Account number' : 'Number';
            $lines[] = $label . ': ' . $method['number'];
        }
        if (! empty($method['account_name'])) {
            $lines[] = 'Account name: ' . $method['account_name'];
        }
        if (! empty($method['bank_name'])) {
            $lines[] = 'Bank: ' . $method['bank_name'];
        }
        if (! empty($method['branch'])) {
            $lines[] = 'Branch: ' . $method['branch'];
        }
        if (! empty($method['swift'])) {
            $lines[] = 'SWIFT: ' . $method['swift'];
        }
        if (! empty($method['payment_url'])) {
            $lines[] = 'Pay online: ' . $method['payment_url'];
        }
        if (! empty($method['instructions'])) {
            $lines[] = $method['instructions'];
        }

        return $lines;
    }

    /**
     * Payload for donate page JS.
     *
     * @return array{methods: list<array<string, mixed>>, gateway: array<string, mixed>}
     */
    public static function frontendConfig(?Setting $setting = null): array
    {
        $setting ??= Setting::firstOrEmpty();
        $gateway = static::gatewayConfig($setting);

        $methods = array_map(function (array $method) use ($setting) {
            $completion = static::completionMode($method, $setting);

            return [
                'id' => $method['id'],
                'label' => $method['label'],
                'channel' => static::channel($method),
                'completion' => $completion,
                'number' => $method['number'] ?? '',
                'account_name' => $method['account_name'] ?? '',
                'bank_name' => $method['bank_name'] ?? '',
                'branch' => $method['branch'] ?? '',
                'swift' => $method['swift'] ?? '',
                'payment_url' => $method['payment_url'] ?? '',
                'instructions' => $method['instructions'] ?? '',
                'details' => static::detailLines($method),
            ];
        }, static::enabled($setting));

        return [
            'methods' => $methods,
            'gateway' => $gateway,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $stored
     * @return list<array<string, mixed>>
     */
    protected static function mergeWithDefaults(array $stored): array
    {
        $byId = [];
        foreach (static::defaults() as $default) {
            $byId[$default['id']] = $default;
        }

        foreach ($stored as $row) {
            $id = (string) ($row['id'] ?? '');
            if ($id === '' || ! isset($byId[$id])) {
                continue;
            }
            $byId[$id] = array_merge($byId[$id], $row);
            $byId[$id]['enabled'] = ! empty($row['enabled']);
        }

        return array_values($byId);
    }

    /**
     * @param  array<string, array<string, mixed>>  $input
     * @return list<array<string, mixed>>
     */
    public static function fromAdminInput(array $input): array
    {
        $methods = static::defaults();

        foreach ($methods as $index => $method) {
            $id = $method['id'];
            $row = $input[$id] ?? [];
            $methods[$index] = array_merge($method, [
                'label' => trim((string) ($row['label'] ?? $method['label'])),
                'enabled' => ! empty($row['enabled']),
                'number' => trim((string) ($row['number'] ?? '')),
                'account_name' => trim((string) ($row['account_name'] ?? '')),
                'bank_name' => trim((string) ($row['bank_name'] ?? '')),
                'branch' => trim((string) ($row['branch'] ?? '')),
                'swift' => trim((string) ($row['swift'] ?? '')),
                'payment_url' => trim((string) ($row['payment_url'] ?? '')),
                'instructions' => trim((string) ($row['instructions'] ?? '')),
            ]);
        }

        return $methods;
    }
}
