@php
    use App\Support\MercyTidesContent;

    $singleSelect = $singleSelect ?? true;
    $formPrefix = $formPrefix ?? 'gi';
    $compactLayout = $compactLayout ?? false;
    $wayColClass = $compactLayout ? 'col-sm-6' : 'col-sm-6 col-lg-4';
    $countriesListId = $countriesListId ?? ($formPrefix . '-countries');
    $wayCards = $wayCards ?? MercyTidesContent::getInvolvedWayCards();
    $oldWays = (array) old('ways', $selectedWays ?? []);
    if ($singleSelect && count($oldWays) > 1) {
        $oldWays = [reset($oldWays)];
    }
    $waysLabel = $waysLabel ?? 'Ways to support mothers';
    $waysHint = $waysHint ?? ($singleSelect
        ? 'Choose the support focus that best fits how you want to help.'
        : 'Select all that apply.');
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
        <label class="form-label mb-2">{{ $waysLabel }} <span class="text-danger">*</span></label>
        <p class="text-muted small mb-3 js-get-involved-way-hint">{{ $waysHint }}</p>
        <div class="row g-3 get-involved-way-cards" role="{{ $singleSelect ? 'radiogroup' : 'group' }}" aria-label="{{ $waysLabel }}">
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
        <div class="invalid-feedback d-block js-get-involved-way-error" hidden>Please choose a way to support.</div>
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

    <div class="col-12">
        <label class="form-label">Commitment description <span class="text-danger">*</span></label>
        <textarea name="message" class="form-control" rows="4" required placeholder="Describe how you want to support young mothers through this option…">{{ old('message') }}</textarea>
    </div>

    @include('frontend.includes.public-form-delivery', [
        'formType' => 'get_involved',
        'whatsappFirst' => true,
        'whatsappLabel' => 'Send via WhatsApp',
        'emailLabel' => 'Send via Email',
        'hideWhatsappNote' => true,
    ])
</form>
