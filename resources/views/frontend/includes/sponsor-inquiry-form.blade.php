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
    $firstName = explode(' ', $profile->displayName())[0] ?? $profile->displayName();
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
        <div class="sponsor-selected-focus js-sponsor-selected-focus{{ $selectedOption ? '' : ' d-none' }}">
            <div class="sponsor-selected-focus__icon" aria-hidden="true">
                <i class="fas js-sponsor-selected-icon {{ $selectedOption['icon'] ?? 'fa-heart' }}"></i>
            </div>
            <div>
                <p class="sponsor-selected-focus__eyebrow mb-1">You’re committing to</p>
                <h3 class="h5 mb-1 js-sponsor-modal-title">{{ $selectedOption['label'] ?? 'Support '.$firstName }}</h3>
                <p class="text-muted small mb-0 js-sponsor-selected-text">{{ $selectedOption['text'] ?? '' }}</p>
            </div>
        </div>
        <p class="text-muted small mt-3 mb-0">
            Tell us your name and how you want to help {{ $firstName }}.
            <strong>No payment is taken on this page.</strong>
        </p>
        <div class="invalid-feedback d-block js-sponsor-focus-error" hidden>Please choose a way to support from the list on the page.</div>
    </div>

    <div class="col-12">
        <label class="form-label">Your full name <span class="text-danger">*</span></label>
        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}" autocomplete="name" placeholder="Your name">
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" autocomplete="tel" placeholder="So we can follow up">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com">
    </div>

    <div class="col-12">
        <label class="form-label">Commitment description <span class="text-danger">*</span></label>
        <textarea
            name="message"
            class="form-control"
            rows="4"
            required
            placeholder="Describe how you want to support {{ $firstName }} through this option…"
        >{{ old('message') }}</textarea>
    </div>

    <div class="col-12">
        <hr class="my-2">
        <p class="text-muted small mb-3">Your commitment is saved on our site, then WhatsApp or email opens — tap <strong>Send</strong> to reach our team. We will share secure giving options separately.</p>
    </div>

    @include('frontend.includes.public-form-delivery', [
        'formType' => 'sponsor_inquiry',
        'whatsappFirst' => true,
        'whatsappLabel' => 'Send commitment via WhatsApp',
        'emailLabel' => 'Send commitment via Email',
    ])
</form>
