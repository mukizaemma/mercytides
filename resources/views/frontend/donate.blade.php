@extends('layouts.frontbase')

@section('title', 'Donate')

@php
    use App\Support\DonationPaymentMethods as DPM;
    $methodIcons = [
        'mtn_momo' => 'fa-mobile-alt',
        'airtel_money' => 'fa-mobile-alt',
        'bank_transfer' => 'fa-university',
        'online_payment' => 'fa-credit-card',
    ];
@endphp

@section('content')
@include('frontend.includes.page-header', [
    'title' => 'Donate',
    'caption' => $sponsorProfile
        ? 'You are giving toward sponsorship for ' . $sponsorProfile->names . ' — thank you for walking alongside her.'
        : 'Your gift equips mothers with skills, hope, and Christ-centered community support in Uganda.',
    'pageKey' => 'donate',
    'image' => $headerImage ?? null,
])

<section class="py-5 grey-bg donate-page">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm site-form-card h-100 donate-page__aside">
                    <div class="card-body p-4">
                        <span class="donate-page__eyebrow">Why give</span>
                        <h2 class="h5 mb-3">Your gift creates lasting change</h2>
                        <ul class="donate-page__benefits list-unstyled mb-0">
                            <li><i class="fas fa-graduation-cap" aria-hidden="true"></i> Vocational training &amp; mentorship</li>
                            <li><i class="fas fa-hands-helping" aria-hidden="true"></i> Christ-centered care for mothers &amp; children</li>
                            <li><i class="fas fa-solar-panel" aria-hidden="true"></i> Solar &amp; community support where needed</li>
                        </ul>
                        <hr class="my-4">
                        <p class="small text-muted mb-0">Prefer to sponsor someone specific? <a href="{{ route('sponsorship.youngMother') }}">View sponsorship profiles</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xl-7">
                <div class="card border-0 shadow-sm site-form-card">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 mb-1">Make a gift</h2>
                        <p class="text-muted mb-4">Choose how you would like to give, enter your details, then follow the steps for your selected option.</p>

                        @if($sponsorProfile)
                            <div class="alert alert-info py-2 mb-4 d-flex align-items-center gap-3">
                                @if(!empty($sponsorProfile->image))
                                    <img src="{{ \App\Support\PageHeaderImage::sponsorshipUrl($sponsorProfile) }}" alt="" class="rounded-circle flex-shrink-0" width="48" height="48" style="object-fit:cover;">
                                @endif
                                <span class="small mb-0">Sponsoring <strong>{{ $sponsorProfile->names }}</strong> — we will link your gift to her profile.</span>
                            </div>
                        @endif

                        @if(count($donationPaymentMethods) === 0)
                            <div class="alert alert-warning">Donation payment options are not available online yet. Please <a href="{{ route('contacts') }}">contact us</a> to give.</div>
                        @else
                        <form class="row g-3 site-partner-form js-public-form js-donate-form" data-form-type="donate" method="post" action="#" novalidate autocomplete="off">
                            @if(request('sponsor'))
                                <input type="hidden" name="sponsorship_id" value="{{ request('sponsor') }}">
                            @endif

                            <div class="col-12 donate-step donate-step--methods">
                                <label class="form-label d-block mb-2">Step 1 — How would you like to give? <span class="text-danger">*</span></label>
                                <div class="donate-payment-method-choices donate-payment-method-choices--tiles">
                                    @foreach($donationPaymentMethods as $index => $method)
                                        @php
                                            $icon = $methodIcons[$method['id']] ?? 'fa-hand-holding-heart';
                                            $completion = DPM::completionMode($method);
                                        @endphp
                                        <label class="donate-payment-method-choice donate-payment-method-choice--tile">
                                            <input type="radio" name="payment_method" value="{{ $method['id'] }}"
                                                class="donate-payment-method-choice__input"
                                                data-completion="{{ $completion }}"
                                                {{ $index === 0 ? 'required' : '' }} {{ $index === 0 ? 'checked' : '' }}>
                                            <span class="donate-payment-method-choice__box">
                                                <span class="donate-payment-method-choice__icon" aria-hidden="true"><i class="fas {{ $icon }}"></i></span>
                                                <span class="donate-payment-method-choice__title">{{ $method['label'] }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 donate-step">
                                <label class="form-label d-block mb-2">Step 2 — Your gift details</label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full name <span class="text-danger">*</span></label>
                                <input type="text" name="names" class="form-control" required autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required autocomplete="email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (USD) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" min="1" step="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frequency</label>
                                <select name="period" class="form-control w-100">
                                    <option value="One-time">One-time</option>
                                    <option value="Monthly">Monthly</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" value="Uganda" autocomplete="country-name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Program (optional)</label>
                                <select name="program_id" class="form-control w-100">
                                    <option value="">General fund</option>
                                    @foreach($ourPrograms as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message (optional)</label>
                                <textarea name="message" class="form-control" rows="3"></textarea>
                            </div>

                            @include('frontend.includes.donate-method-panels', [
                                'donationPaymentMethods' => $donationPaymentMethods,
                                'setting' => $setting ?? \App\Models\Setting::firstOrEmpty(),
                            ])

                            @include('frontend.includes.donate-form-delivery')
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($donationPaymentMethods ?? []) > 0)
    @push('scripts')
        <script type="application/json" id="donate-payment-config">@json($donationPaymentConfig)</script>
        <script src="{{ asset('assets/js/donate-payment-flow.js') }}" defer></script>
    @endpush
@endif
@endsection
