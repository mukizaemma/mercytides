@php
    use App\Support\MercyTidesContent;

    $singleSelect = $singleSelect ?? true;
    $formPrefix = $formPrefix ?? 'gi';
    $compactLayout = $compactLayout ?? false;
    $wayColClass = $compactLayout ? 'col-sm-6' : 'col-sm-6 col-lg-3';
    $countriesListId = $countriesListId ?? ($formPrefix . '-countries');
    $wayCards = $wayCards ?? MercyTidesContent::getInvolvedWayCards();
    $oldWays = (array) old('ways', $selectedWays ?? []);
    if ($singleSelect && count($oldWays) > 1) {
        $oldWays = [reset($oldWays)];
    }
@endphp

<form class="row g-3 site-partner-form js-public-form js-get-involved-form"
      data-form-type="get_involved"
      data-channel-new-tab="true"
      data-single-way="{{ $singleSelect ? 'true' : 'false' }}"
      method="post"
      action="#"
      novalidate
      autocomplete="off">

    @if(!empty($hiddenFields))
        @foreach($hiddenFields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
    @endif

    <div class="col-12">
        <label class="form-label mb-2">How would you like to get involved? <span class="text-danger">*</span></label>
        <p class="text-muted small mb-3 js-get-involved-way-hint">
            {{ $singleSelect ? 'Choose the option that best fits you.' : 'Select all that apply.' }}
        </p>
        <div class="row g-3 get-involved-way-cards" role="{{ $singleSelect ? 'radiogroup' : 'group' }}" aria-label="How would you like to get involved?">
            @foreach($wayCards as $card)
                <div class="{{ $wayColClass }}">
                    <label class="get-involved-way-card get-involved-way-card--selectable mb-0 {{ in_array($card['key'], $oldWays, true) ? 'is-selected' : '' }}">
                        <input class="visually-hidden js-get-involved-way"
                               type="{{ $singleSelect ? 'radio' : 'checkbox' }}"
                               name="ways[]"
                               value="{{ $card['key'] }}"
                               data-way="{{ $card['key'] }}"
                               id="{{ $formPrefix }}_way_{{ $card['key'] }}"
                               {{ in_array($card['key'], $oldWays, true) ? 'checked' : '' }}
                               @if($singleSelect && $loop->first) required @endif>
                        <span class="get-involved-way-card__icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
                        <span class="get-involved-way-card__title d-block">{{ $card['title'] }}</span>
                        <span class="get-involved-way-card__text d-block">{{ $card['text'] }}</span>
                        <span class="get-involved-way-card__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                    </label>
                </div>
            @endforeach
        </div>
        <div class="invalid-feedback d-block js-get-involved-way-error" hidden>Please select how you would like to get involved.</div>
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
        <input type="text" name="country" class="form-control" required value="{{ old('country', 'Uganda') }}" autocomplete="country-name" list="{{ $countriesListId }}">
        <datalist id="{{ $countriesListId }}">
            <option value="Uganda"></option>
            <option value="Rwanda"></option>
            <option value="Kenya"></option>
            <option value="Tanzania"></option>
            <option value="United States"></option>
            <option value="United Kingdom"></option>
            <option value="Canada"></option>
            <option value="Germany"></option>
            <option value="Netherlands"></option>
            <option value="Australia"></option>
        </datalist>
    </div>

    <div class="col-12 js-get-involved-panel" data-panel="donation" hidden>
        <label class="form-label">Donation amount (USD) <span class="text-danger">*</span></label>
        <div class="get-involved-donation-chips mb-3" role="group" aria-label="Suggested donation amounts">
            @foreach([25, 50, 100] as $amount)
                <button type="button" class="get-involved-donation-chip js-donation-chip" data-amount="{{ $amount }}">${{ $amount }}</button>
            @endforeach
            <button type="button" class="get-involved-donation-chip js-donation-chip" data-amount="custom">Other</button>
        </div>
        <div class="input-group js-donation-custom-wrap" hidden>
            <span class="input-group-text">$</span>
            <input type="number" name="donation_amount" class="form-control js-donation-amount-input" min="1" step="1" value="{{ old('donation_amount') }}" placeholder="Enter amount">
        </div>
        <div class="invalid-feedback d-block js-donation-amount-error" hidden>Please choose or enter a donation amount.</div>
        <small class="text-muted d-block mt-2">We will follow up with secure giving instructions after you send your message.</small>
    </div>

    <div class="col-12 js-get-involved-panel" data-panel="volunteer" hidden>
        <label class="form-label">Areas of interest &amp; experience <span class="text-danger">*</span></label>
        <textarea name="volunteer_experience" class="form-control" rows="4" placeholder="Tell us your skills, availability, and how you would like to serve…">{{ old('volunteer_experience') }}</textarea>
    </div>

    <div class="col-12 js-get-involved-panel" data-panel="partner" hidden>
        <label class="form-label">Partnership vision &amp; expected impact <span class="text-danger">*</span></label>
        <textarea name="partnership_details" class="form-control" rows="4" placeholder="Describe the kind of partnership you are looking for and the impact you hope to make…">{{ old('partnership_details') }}</textarea>
    </div>

    <div class="col-12 js-get-involved-panel" data-panel="visit_mothers" hidden>
        <div class="alert alert-light border mb-0 small">
            <i class="fas fa-info-circle me-1 text-primary" aria-hidden="true"></i>
            Thank you for your heart to visit. Share any preferred dates or questions in the notes below — our team will coordinate with you.
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">Additional notes <span class="text-muted">(optional)</span></label>
        <textarea name="message" class="form-control" rows="3" placeholder="Anything else we should know…">{{ old('message') }}</textarea>
    </div>

    <div class="col-12">
        <hr class="my-2">
        <h3 class="h6 mb-2">Send your request</h3>
        <p class="text-muted small mb-1">Your details are saved on our site, then your app opens in a new tab — tap <strong>Send</strong> to reach our team.</p>
        <p class="text-muted small mb-3">We typically respond within 2–3 business days.</p>
    </div>

    @include('frontend.includes.public-form-delivery', ['formType' => 'get_involved', 'whatsappFirst' => true])
</form>
