<?php

namespace App\Services;

use App\Models\FormSubmission;
use App\Models\Program;
use App\Models\Setting;
use App\Support\DonationPaymentMethods;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use ReCaptcha\ReCaptcha;

class FormSubmissionService
{
    public const CHANNEL_WHATSAPP = 'whatsapp';

    public const CHANNEL_EMAIL = 'email';

    /** @var array<string, string> */
    public const FORM_TYPES = [
        'donate' => 'Donation pledge',
        'volunteer' => 'Volunteer application',
        'support_application' => 'Apply for support',
        'partnership' => 'Partnership inquiry',
        'get_involved' => 'Get involved',
        'sponsor_inquiry' => 'Sponsorship inquiry',
        'order_request' => 'Order request',
    ];

    public function process(Request $request): array
    {
        $this->guardAgainstBots($request);

        $formType = (string) $request->input('form_type');
        $channel = (string) $request->input('channel');

        if (! array_key_exists($formType, self::FORM_TYPES)) {
            throw ValidationException::withMessages(['form_type' => 'Invalid form type.']);
        }

        if (! in_array($channel, [self::CHANNEL_WHATSAPP, self::CHANNEL_EMAIL], true)) {
            throw ValidationException::withMessages(['channel' => 'Choose WhatsApp or Email to send your details.']);
        }

        $payload = $this->validatePayload($request, $formType);
        $message = $this->buildPlainTextMessage($formType, $payload);
        $setting = Setting::firstOrEmpty();

        if ($channel === self::CHANNEL_WHATSAPP) {
            $openUrl = $this->buildWhatsAppUrl($setting, $message);
        } else {
            $openUrl = $this->buildMailtoUrl($setting, $formType, $payload, $message);
        }

        $submission = FormSubmission::create([
            'form_type' => $formType,
            'channel' => $channel,
            'submitter_name' => $this->extractName($formType, $payload),
            'submitter_email' => $payload['email'] ?? null,
            'submitter_phone' => $payload['phone'] ?? $payload['phone_whatsapp'] ?? null,
            'payload' => $payload,
            'message_preview' => Str::limit($message, 500),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
        ]);

        return [
            'submission_id' => $submission->id,
            'open_url' => $openUrl,
            'message' => $channel === self::CHANNEL_WHATSAPP
                ? 'Your details were saved. WhatsApp should open — tap Send to reach our team.'
                : 'Your details were saved. Your email app should open — tap Send to reach our team.',
        ];
    }

    public function guardAgainstBots(Request $request): void
    {
        $ipKey = 'form-submission:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 8)) {
            throw ValidationException::withMessages([
                'form' => 'Too many attempts. Please wait a few minutes and try again.',
            ]);
        }
        RateLimiter::hit($ipKey, 10 * 60);

        $request->validate([
            'website' => ['nullable', 'max:0'],
            'started_at' => ['nullable', 'integer'],
        ]);

        $startedAt = (int) $request->input('started_at', 0);
        if ($startedAt > 0 && (time() - $startedAt) < 4) {
            throw ValidationException::withMessages([
                'form' => 'Please take a moment to review your details before sending.',
            ]);
        }

        $this->verifyRecaptcha($request);
    }

    public function verifyRecaptcha(Request $request): void
    {
        $secret = $this->recaptchaSecret();
        if ($secret === '') {
            return;
        }

        $token = (string) $request->input('g-recaptcha-response', '');
        if ($token === '') {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Please complete the reCAPTCHA check.',
            ]);
        }

        $recaptcha = new ReCaptcha($secret);
        $response = $recaptcha->verify($token, $request->ip());

        if (! $response->isSuccess()) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
            ]);
        }
    }

    public function recaptchaSiteKey(): string
    {
        $setting = Setting::firstOrEmpty();

        return (string) ($setting->recaptcha_site_key ?? config('services.recaptcha.site_key', ''));
    }

    public function recaptchaSecret(): string
    {
        $setting = Setting::firstOrEmpty();

        return (string) ($setting->recaptcha_secret_key ?? config('services.recaptcha.secret', ''));
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePayload(Request $request, string $formType): array
    {
        $rules = match ($formType) {
            'donate' => [
                'names' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'amount' => ['required', 'numeric', 'min:1'],
                'period' => ['nullable', 'string', 'max:50'],
                'country' => ['nullable', 'string', 'max:120'],
                'program_id' => ['nullable', 'integer'],
                'sponsorship_id' => ['nullable', 'integer'],
                'message' => ['nullable', 'string', 'max:2000'],
                'payment_method' => ['required', 'string', 'max:64'],
            ],
            'volunteer' => [
                'names' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:64'],
                'address' => ['nullable', 'string', 'max:255'],
                'country' => ['required', 'string', 'max:120'],
                'aboutYou' => ['nullable', 'string', 'max:5000'],
                'career' => ['nullable', 'string', 'max:5000'],
                'howToServe' => ['required', 'string', 'max:5000'],
                'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            ],
            'support_application' => [
                'names' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:50'],
                'email' => ['nullable', 'email', 'max:255'],
                'district' => ['nullable', 'string', 'max:120'],
                'address' => ['nullable', 'string', 'max:255'],
                'age' => ['nullable', 'string', 'max:20'],
                'gender' => ['nullable', 'string', 'max:30'],
                'child_info' => ['nullable', 'string', 'max:2000'],
                'challenge' => ['required', 'string', 'max:5000'],
            ],
            'partnership' => [
                'organization' => ['nullable', 'string', 'max:255'],
                'full_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:64'],
                'email' => ['required', 'email', 'max:255'],
                'interests' => ['nullable', 'array'],
                'interests.*' => ['string', Rule::in(array_keys($this->partnershipInterestLabels()))],
                'message' => ['nullable', 'string', 'max:20000'],
                'source_page' => ['nullable', 'string', 'max:64'],
            ],
            'get_involved' => [
                'full_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:64'],
                'email' => ['required', 'email', 'max:255'],
                'country' => ['required', 'string', 'max:120'],
                'ways' => ['required', 'array', 'min:1'],
                'ways.*' => ['string', Rule::in(array_keys($this->getInvolvedWayLabels()))],
                'donation_amount' => ['nullable', 'numeric', 'min:1'],
                'volunteer_experience' => ['nullable', 'string', 'max:5000'],
                'partnership_details' => ['nullable', 'string', 'max:5000'],
                'message' => ['nullable', 'string', 'max:2000'],
            ],
            'sponsor_inquiry' => [
                'full_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:64'],
                'email' => ['required', 'email', 'max:255'],
                'country' => ['required', 'string', 'max:120'],
                'sponsorship_id' => ['required', 'integer', 'exists:sponsorships,id'],
                'donation_preference' => ['required', 'string', Rule::in(array_keys(\App\Support\MercyTidesContent::sponsorshipDonationPreferences()))],
                'donation_amount' => ['nullable', 'numeric', 'min:1'],
                'message' => ['nullable', 'string', 'max:2000'],
            ],
            'order_request' => [
                'full_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:64'],
                'email' => ['required', 'email', 'max:255'],
                'product_description' => ['required', 'string', 'max:20000'],
                'product_slug' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };

        $validated = $request->validate($rules);

        if ($formType === 'donate') {
            $allowedPaymentMethods = DonationPaymentMethods::enabledIds();
            if ($allowedPaymentMethods === []) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Donation payment options are not configured yet. Please contact us directly.',
                ]);
            }
            if (! in_array((string) ($validated['payment_method'] ?? ''), $allowedPaymentMethods, true)) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Please choose a valid way to give.',
                ]);
            }
        }

        if ($formType === 'partnership') {
            if (empty($validated['interests'] ?? []) && empty($validated['message'] ?? '')) {
                throw ValidationException::withMessages([
                    'interests' => 'Select at least one area of interest or write a message.',
                ]);
            }
        }

        if ($formType === 'get_involved') {
            $ways = (array) ($validated['ways'] ?? []);
            if (in_array('donation', $ways, true) && empty($validated['donation_amount'])) {
                throw ValidationException::withMessages([
                    'donation_amount' => 'Please enter the amount you would like to give in USD.',
                ]);
            }
            if (in_array('volunteer', $ways, true) && trim((string) ($validated['volunteer_experience'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'volunteer_experience' => 'Please describe your areas of interest and experience.',
                ]);
            }
            if (in_array('partner', $ways, true) && trim((string) ($validated['partnership_details'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'partnership_details' => 'Please describe the partnership you are looking for and the impact you hope to make.',
                ]);
            }
        }

        if ($formType === 'sponsor_inquiry') {
            $preference = (string) ($validated['donation_preference'] ?? '');
            if (in_array($preference, ['monthly', 'one_time'], true) && empty($validated['donation_amount'])) {
                throw ValidationException::withMessages([
                    'donation_amount' => 'Please enter the amount you would like to give in USD.',
                ]);
            }
        }

        $this->rejectSuspiciousText($validated, $formType);

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function rejectSuspiciousText(array $payload, string $formType): void
    {
        $spamPattern = '/https?:\/\/|www\./i';
        $textFields = match ($formType) {
            'donate' => ['names', 'message'],
            'volunteer' => ['names', 'aboutYou', 'career', 'howToServe'],
            'support_application' => ['names', 'challenge', 'child_info'],
            'partnership' => ['organization', 'full_name', 'message'],
            'get_involved' => ['full_name', 'volunteer_experience', 'partnership_details', 'message'],
            'sponsor_inquiry' => ['full_name', 'message'],
            'order_request' => ['full_name', 'product_description'],
            default => [],
        };

        foreach ($textFields as $field) {
            $value = (string) ($payload[$field] ?? '');
            if ($value !== '' && preg_match($spamPattern, $value)) {
                throw ValidationException::withMessages([
                    $field => 'Please remove web links from this field.',
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function extractName(string $formType, array $payload): ?string
    {
        return match ($formType) {
            'partnership', 'order_request', 'get_involved', 'sponsor_inquiry' => $payload['full_name'] ?? null,
            default => $payload['names'] ?? null,
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function buildPlainTextMessage(string $formType, array $payload): string
    {
        $lines = [
            self::FORM_TYPES[$formType] ?? $formType,
            'Mercy Tides Foundation — website form',
            '—',
        ];

        switch ($formType) {
            case 'donate':
                $lines[] = 'Name: ' . ($payload['names'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                $lines[] = 'Amount (USD): ' . ($payload['amount'] ?? '');
                $lines[] = 'Frequency: ' . ($payload['period'] ?? 'One-time');
                if (! empty($payload['country'])) {
                    $lines[] = 'Country: ' . $payload['country'];
                }
                if (! empty($payload['program_id'])) {
                    $program = Program::find($payload['program_id']);
                    $lines[] = 'Program: ' . ($program->title ?? $payload['program_id']);
                }
                if (! empty($payload['sponsorship_id'])) {
                    $mother = Sponsorship::find($payload['sponsorship_id']);
                    $lines[] = 'Sponsor mother: ' . ($mother->names ?? '#' . $payload['sponsorship_id']);
                }
                if (! empty($payload['message'])) {
                    $lines[] = 'Message: ' . $payload['message'];
                }
                if (! empty($payload['payment_method'])) {
                    $method = DonationPaymentMethods::find((string) $payload['payment_method']);
                    $lines[] = 'Preferred way to give: ' . ($method['label'] ?? $payload['payment_method']);
                    if ($method) {
                        foreach (DonationPaymentMethods::detailLines($method) as $detailLine) {
                            $lines[] = '  ' . $detailLine;
                        }
                    }
                }
                break;

            case 'volunteer':
                $lines[] = 'Name: ' . ($payload['names'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                if (! empty($payload['phone'])) {
                    $lines[] = 'Phone: ' . $payload['phone'];
                }
                $lines[] = 'Country: ' . ($payload['country'] ?? '');
                if (! empty($payload['address'])) {
                    $lines[] = 'Location: ' . $payload['address'];
                }
                if (! empty($payload['program_id'])) {
                    $program = Program::find($payload['program_id']);
                    $lines[] = 'Program interest: ' . ($program->title ?? $payload['program_id']);
                }
                if (! empty($payload['aboutYou'])) {
                    $lines[] = 'About: ' . $payload['aboutYou'];
                }
                if (! empty($payload['career'])) {
                    $lines[] = 'Skills: ' . $payload['career'];
                }
                $lines[] = 'How to serve: ' . ($payload['howToServe'] ?? '');
                break;

            case 'support_application':
                $lines[] = 'Name: ' . ($payload['names'] ?? '');
                $lines[] = 'Phone: ' . ($payload['phone'] ?? '');
                if (! empty($payload['email'])) {
                    $lines[] = 'Email: ' . $payload['email'];
                }
                if (! empty($payload['district'])) {
                    $lines[] = 'District: ' . $payload['district'];
                }
                if (! empty($payload['age'])) {
                    $lines[] = 'Age: ' . $payload['age'];
                }
                if (! empty($payload['gender'])) {
                    $lines[] = 'Gender: ' . $payload['gender'];
                }
                if (! empty($payload['address'])) {
                    $lines[] = 'Address: ' . $payload['address'];
                }
                if (! empty($payload['child_info'])) {
                    $lines[] = 'Children: ' . $payload['child_info'];
                }
                $lines[] = 'Need: ' . ($payload['challenge'] ?? '');
                break;

            case 'partnership':
                if (! empty($payload['organization'])) {
                    $lines[] = 'Organisation: ' . $payload['organization'];
                }
                $lines[] = 'Name: ' . ($payload['full_name'] ?? '');
                $lines[] = 'Phone: ' . ($payload['phone'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                $interests = $this->formatPartnershipInterests((array) ($payload['interests'] ?? []));
                if ($interests !== '') {
                    $lines[] = 'Interests: ' . $interests;
                }
                if (! empty($payload['message'])) {
                    $lines[] = 'Message: ' . $payload['message'];
                }
                if (! empty($payload['source_page'])) {
                    $lines[] = 'From page: ' . $payload['source_page'];
                }
                break;

            case 'get_involved':
                $lines[] = 'Name: ' . ($payload['full_name'] ?? '');
                $lines[] = 'Phone: ' . ($payload['phone'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                $lines[] = 'Country: ' . ($payload['country'] ?? '');
                $ways = $this->formatGetInvolvedWays((array) ($payload['ways'] ?? []));
                if ($ways !== '') {
                    $lines[] = 'Ways to get involved: ' . $ways;
                }
                if (! empty($payload['donation_amount'])) {
                    $lines[] = 'Donation amount (USD): ' . $payload['donation_amount'];
                }
                if (! empty($payload['volunteer_experience'])) {
                    $lines[] = 'Volunteer interests & experience: ' . $payload['volunteer_experience'];
                }
                if (! empty($payload['partnership_details'])) {
                    $lines[] = 'Partnership details: ' . $payload['partnership_details'];
                }
                if (! empty($payload['message'])) {
                    $lines[] = 'Additional notes: ' . $payload['message'];
                }
                break;

            case 'sponsor_inquiry':
                $profile = Sponsorship::query()->find($payload['sponsorship_id'] ?? null);
                $lines[] = 'Name: ' . ($payload['full_name'] ?? '');
                $lines[] = 'Phone: ' . ($payload['phone'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                $lines[] = 'Country: ' . ($payload['country'] ?? '');
                if ($profile) {
                    $lines[] = 'Sponsorship profile: ' . $profile->displayName();
                    $lines[] = 'Category: ' . $profile->typeLabel();
                    if (! empty($profile->monthly_need)) {
                        $lines[] = 'Suggested monthly need (USD): ' . $profile->monthly_need;
                    }
                }
                $preferences = \App\Support\MercyTidesContent::sponsorshipDonationPreferences();
                $prefKey = (string) ($payload['donation_preference'] ?? '');
                $lines[] = 'How they want to give: ' . ($preferences[$prefKey] ?? $prefKey);
                if (! empty($payload['donation_amount'])) {
                    $lines[] = 'Amount (USD): ' . $payload['donation_amount'];
                }
                if (! empty($payload['message'])) {
                    $lines[] = 'Message: ' . $payload['message'];
                }
                break;

            case 'order_request':
                $lines[] = 'Name: ' . ($payload['full_name'] ?? '');
                $lines[] = 'Phone: ' . ($payload['phone'] ?? '');
                $lines[] = 'Email: ' . ($payload['email'] ?? '');
                if (! empty($payload['product_slug'])) {
                    $lines[] = 'Product: ' . $payload['product_slug'];
                }
                $lines[] = 'Request: ' . ($payload['product_description'] ?? '');
                break;
        }

        return implode("\n", $lines);
    }

    protected function buildWhatsAppUrl(Setting $setting, string $message): string
    {
        $phone = preg_replace('/\D+/', '', (string) ($setting->phone ?? $setting->phone1 ?? ''));
        if ($phone === '') {
            throw ValidationException::withMessages([
                'form' => 'WhatsApp is not configured. Please use Email or contact us directly.',
            ]);
        }

        $text = Str::limit($message, 1500, '…');

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($text);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function buildMailtoUrl(Setting $setting, string $formType, array $payload, string $message): string
    {
        $email = trim((string) ($setting->email ?? ''));
        if ($email === '') {
            throw ValidationException::withMessages([
                'form' => 'Email is not configured on the site. Please use WhatsApp or call us.',
            ]);
        }

        $subject = (self::FORM_TYPES[$formType] ?? 'Inquiry') . ' — Mercy Tides Foundation';
        $replyTo = $payload['email'] ?? '';
        $body = $message;
        if ($replyTo !== '') {
            $body .= "\n\n—\nReply to guest: " . $replyTo;
        }

        return 'mailto:' . $email
            . '?subject=' . rawurlencode($subject)
            . '&body=' . rawurlencode(Str::limit($body, 1800, '…'));
    }

    /**
     * @return array<string, string>
     */
    protected function getInvolvedWayLabels(): array
    {
        return \App\Support\MercyTidesContent::getInvolvedWays();
    }

    /**
     * @param  list<string>  $keys
     */
    protected function formatGetInvolvedWays(array $keys): string
    {
        $labels = $this->getInvolvedWayLabels();
        $parts = [];
        foreach ($keys as $key) {
            $parts[] = $labels[$key] ?? $key;
        }

        return implode(', ', $parts);
    }

    /**
     * @return array<string, string>
     */
    protected function partnershipInterestLabels(): array
    {
        return \App\Support\MercyTidesContent::partnershipInterestLabels();
    }

    /**
     * @param  list<string>  $keys
     */
    protected function formatPartnershipInterests(array $keys): string
    {
        $labels = $this->partnershipInterestLabels();
        $parts = [];
        foreach ($keys as $key) {
            $parts[] = $labels[$key] ?? $key;
        }

        return implode(', ', $parts);
    }

    /**
     * @return array{total: int, by_channel: array<string, int>, by_form: array<string, int>}
     */
    public static function aggregateStats(): array
    {
        $rows = FormSubmission::query()
            ->selectRaw('form_type, channel, COUNT(*) as total')
            ->groupBy('form_type', 'channel')
            ->get();

        $byChannel = ['whatsapp' => 0, 'email' => 0];
        $byForm = [];

        foreach ($rows as $row) {
            $byChannel[$row->channel] = ($byChannel[$row->channel] ?? 0) + (int) $row->total;
            $byForm[$row->form_type] = ($byForm[$row->form_type] ?? 0) + (int) $row->total;
        }

        return [
            'total' => array_sum($byChannel),
            'by_channel' => $byChannel,
            'by_form' => $byForm,
        ];
    }
}
