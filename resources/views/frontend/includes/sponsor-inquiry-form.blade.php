@php
    use App\Support\MercyTidesContent;
    use App\Support\SponsorshipSupportOptions;

    $selectedFocus = old('support_focus', $preselectedSupportFocus ?? '');
    $selectedOption = $selectedFocus !== '' ? SponsorshipSupportOptions::find($selectedFocus) : null;
    if (! $selectedOption && $selectedFocus !== '') {
        $selectedOption = [
            'key' => $selectedFocus,
            'label' => MercyTidesContent::sponsorshipSupportFocusLabels()[$selectedFocus] ?? $selectedFocus,
            'text' => '',
            'icon' => 'fa-heart',
        ];
    }

    $motherName = $profile->displayName();
    $whyMessage = trim((string) ($commitmentWhy ?? $storyExcerpt ?? ''));
    if ($whyMessage === '') {
        $whyMessage = 'Your support helps '.$motherName.' move toward skills, dignity, and a steadier path for her family.';
    }
@endphp

<form class="row g-3 site-partner-form js-public-form js-sponsor-inquiry-form"
      data-form-type="sponsor_inquiry"
      data-channel-new-tab="true"
      method="post"
      action="#"
      novalidate
      autocomplete="off">

    <input type="hidden" name="sponsorship_id" value="{{ $profile->id }}">
    <input type="hidden" name="support_focus" class="js-sponsor-focus-value" value="{{ $selectedFocus }}" required>
    <input type="hidden" name="donation_preference" value="pledge">
    <input type="hidden" name="country" value="{{ old('country', 'Uganda') }}">

    <div class="col-12">
        <div class="sponsor-commitment-summary">
            <h3 class="sponsor-commitment-summary__name js-sponsor-mother-name">{{ $motherName }}</h3>
            <p class="sponsor-commitment-summary__way js-sponsor-selected-label{{ $selectedOption ? '' : ' d-none' }}">
                {{ $selectedOption['label'] ?? '' }}
            </p>
            <p class="sponsor-commitment-summary__why js-sponsor-why-message">{{ $whyMessage }}</p>
        </div>
        <div class="invalid-feedback d-block js-sponsor-focus-error" hidden>Please choose a way to support from the list, then try again.</div>
    </div>

    <div class="col-12">
        <label class="form-label">Your full name <span class="text-danger">*</span></label>
        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}" autocomplete="name" placeholder="Your name">
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" autocomplete="tel">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="{{ old('email') }}" autocomplete="email">
    </div>

    <div class="col-12">
        <label class="form-label">Commitment description <span class="text-danger">*</span></label>
        <textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
    </div>

    @include('frontend.includes.public-form-delivery', [
        'formType' => 'sponsor_inquiry',
        'whatsappFirst' => true,
        'whatsappLabel' => 'Send via WhatsApp',
        'emailLabel' => 'Send via Email',
        'hideWhatsappNote' => true,
    ])
</form>
