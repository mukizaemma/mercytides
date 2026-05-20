<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_type',
        'channel',
        'submitter_name',
        'submitter_email',
        'submitter_phone',
        'payload',
        'message_preview',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public static function formTypeLabels(): array
    {
        return [
            'donate' => 'Donation pledge',
            'volunteer' => 'Volunteer application',
            'support_application' => 'Apply for support',
            'partnership' => 'Partnership inquiry',
            'order_request' => 'Order request',
        ];
    }

    public function formTypeLabel(): string
    {
        return static::formTypeLabels()[$this->form_type] ?? ucfirst(str_replace('_', ' ', $this->form_type));
    }

    public function channelLabel(): string
    {
        return $this->channel === 'whatsapp' ? 'WhatsApp' : 'Email';
    }
}
