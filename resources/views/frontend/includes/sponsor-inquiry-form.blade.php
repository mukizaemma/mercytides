@php
    use App\Support\MercyTidesContent;

    $preferences = MercyTidesContent::sponsorshipDonationPreferences();
    $oldPreference = old('donation_preference');
@endphp

<form class="row g-3 site-partner-form js-public-form js-sponsor-inquiry-form"
      data-form-type="sponsor_inquiry"
      data-channel-new-tab="true"
      method="post"
      action="#"
      novalidate
      autocomplete="off">

    <input type="hidden" name="sponsorship_id" value="{{ $profile->id }}">

    <div class="col-12">
        <h3 class="h5 mb-2">Sponsor {{ $profile->displayName() }}</h3>
        <p class="text-muted small mb-0">Share your contact details and how you would like to give. We will follow up with secure next steps.</p>
    </div>

    <div class="col-md-6">
        <label class="form-label">Full name <span class="text-danger">*</span></label>
        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}" autocomplete="name">
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" autocomplete="tel">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="{{ old('email') }}" autocomplete="email">
    </div>
    <div class="col-md-6">
        <label class="form-label">Country <span class="text-danger">*</span></label>
        <input type="text" name="country" class="form-control" required value="{{ old('country', 'Uganda') }}" autocomplete="country-name">
    </div>

    <div class="col-12">
        <label class="form-label mb-2">How would you like to give? <span class="text-danger">*</span></label>
        <div class="row g-2 sponsor-preference-cards">
            @foreach($preferences as $key => $label)
                <div class="col-sm-6">
                    <label class="get-involved-way-card get-involved-way-card--selectable sponsor-preference-card mb-0 {{ $oldPreference === $key ? 'is-selected' : '' }}">
                        <input class="visually-hidden js-sponsor-preference"
                               type="radio"
                               name="donation_preference"
                               value="{{ $key }}"
                               data-preference="{{ $key }}"
                               {{ $oldPreference === $key ? 'checked' : '' }}
                               @if($loop->first) required @endif>
                        <span class="get-involved-way-card__title d-block">{{ $label }}</span>
                        <span class="get-involved-way-card__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                    </label>
                </div>
            @endforeach
        </div>
        <div class="invalid-feedback d-block js-sponsor-preference-error" hidden>Please choose how you would like to give.</div>
    </div>

    <div class="col-12 js-sponsor-amount-panel" hidden>
        <label class="form-label">Amount (USD) <span class="text-danger">*</span></label>
        @if(!empty($profile->monthly_need))
            <p class="text-muted small">Suggested need: <strong>${{ $profile->monthly_need }}</strong> per month</p>
        @endif
        <div class="get-involved-donation-chips mb-3" role="group" aria-label="Suggested amounts">
            @foreach([25, 50, 100] as $amount)
                <button type="button" class="get-involved-donation-chip js-sponsor-amount-chip" data-amount="{{ $amount }}">${{ $amount }}</button>
            @endforeach
            <button type="button" class="get-involved-donation-chip js-sponsor-amount-chip" data-amount="custom">Other</button>
        </div>
        <div class="input-group js-sponsor-custom-wrap" hidden>
            <span class="input-group-text">$</span>
            <input type="number" name="donation_amount" class="form-control js-sponsor-amount-input" min="1" step="1" value="{{ old('donation_amount', $profile->monthly_need) }}" placeholder="Enter amount">
        </div>
        <div class="invalid-feedback d-block js-sponsor-amount-error" hidden>Please choose or enter an amount.</div>
    </div>

    <div class="col-12">
        <label class="form-label">Message <span class="text-muted">(optional)</span></label>
        <textarea name="message" class="form-control" rows="3" placeholder="Questions, preferred start date, or encouragement for {{ $profile->displayName() }}…">{{ old('message') }}</textarea>
    </div>

    <div class="col-12">
        <hr class="my-2">
        <p class="text-muted small mb-3">Your details are saved on our site, then WhatsApp or email opens in a new tab — tap <strong>Send</strong> to reach our team.</p>
    </div>

    @include('frontend.includes.public-form-delivery', ['formType' => 'sponsor_inquiry', 'whatsappFirst' => true])
</form>
