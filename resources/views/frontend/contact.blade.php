@extends('layouts.frontbase')

@section('title', 'Contact')

@php
    $location = trim((string) ($contact->address ?? ''));
    if ($location === '') {
        $location = 'Uganda';
    }
    $whatsappDigits = preg_replace('/\D+/', '', (string) ($contact->phone ?? $contact->phone1 ?? ''));

    $mapEmbedRaw = trim((string) ($setting->google_map_embed_code ?? ''));
    $showContactMap = (bool) ($setting->show_contact_map ?? false) && $mapEmbedRaw !== '';
    $mapSrc = '';
    $mapIframeHtml = '';
    if ($showContactMap) {
        if (stripos($mapEmbedRaw, '<iframe') !== false) {
            $mapIframeHtml = $mapEmbedRaw;
        } else {
            $mapSrc = $mapEmbedRaw;
        }
    }
@endphp

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Contact',
    'caption' => 'Reach our team in Uganda — and choose how you want to support young mothers.',
    'pageKey' => 'contact',
])

<section class="contact-page pt-90 pb-90 grey-bg">
    <div class="container">
        <div class="row g-3 g-md-4 mb-4 mb-lg-5 contact-page__stats">
            <div class="col-md-4">
                <article class="contact-stat-card h-100">
                    <span class="contact-stat-card__icon" aria-hidden="true"><i class="fas fa-phone"></i></span>
                    <div>
                        <h2 class="contact-stat-card__title">Phone</h2>
                        @if(!empty($contact->phone))
                            <a class="contact-stat-card__value" href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        @endif
                        @if(!empty($contact->phone2))
                            <a class="contact-stat-card__value" href="tel:{{ $contact->phone2 }}">{{ $contact->phone2 }}</a>
                        @endif
                        @if($whatsappDigits !== '')
                            <a class="contact-stat-card__whatsapp" href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-whatsapp me-1" aria-hidden="true"></i> Chat on WhatsApp
                            </a>
                        @endif
                    </div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="contact-stat-card h-100">
                    <span class="contact-stat-card__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                    <div>
                        <h2 class="contact-stat-card__title">Email</h2>
                        @if(!empty($contact->email))
                            <a class="contact-stat-card__value" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                        @endif
                    </div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="contact-stat-card h-100">
                    <span class="contact-stat-card__icon" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                    <div>
                        <h2 class="contact-stat-card__title">Location</h2>
                        <p class="contact-stat-card__value mb-0">{{ $location }}</p>
                    </div>
                </article>
            </div>
        </div>

        @if(!empty($setting->facebook) || !empty($setting->instagram) || !empty($setting->youtube))
            <div class="contact-page__social text-center mb-4 mb-lg-5">
                <span class="contact-page__social-label">Follow Mercy Tides</span>
                <div class="contact-page__social-links">
                    @if(!empty($setting->facebook))
                        <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(!empty($setting->instagram))
                        <a href="{{ $setting->instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if(!empty($setting->youtube))
                        <a href="{{ $setting->youtube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    @endif
                </div>
            </div>
        @endif

        <div class="row g-4 g-xl-5 align-items-stretch{{ $showContactMap ? '' : ' justify-content-center' }}" id="contact-form">
            <div class="{{ $showContactMap ? 'col-lg-6' : 'col-lg-10 col-xl-9' }}">
                <div class="card border-0 shadow-sm site-form-card contact-page__form-card h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 mb-2">Choose a way to support</h2>
                        <p class="text-muted mb-4">Pick one of the five support paths below, share your details, then send via WhatsApp or email.</p>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @include('frontend.includes.get-involved-form', [
                            'singleSelect' => true,
                            'compactLayout' => ! $showContactMap ? false : true,
                            'formPrefix' => 'contact',
                            'countriesListId' => 'contact-countries',
                            'waysLabel' => 'Ways to support mothers',
                            'waysHint' => 'Choose the option that matches how you want to help.',
                        ])
                    </div>
                </div>
            </div>

            @if($showContactMap)
                <div class="col-lg-6">
                    <div class="contact-map-wrap h-100">
                        <div class="contact-map-wrap__header">
                            <h2 class="h5 mb-1">Find us in Uganda</h2>
                            <p class="text-muted small mb-0">Visit by appointment — message us to coordinate a time.</p>
                        </div>

                        @if($mapIframeHtml !== '')
                            <div class="contact-map-wrap__frame">
                                {!! $mapIframeHtml !!}
                            </div>
                        @else
                            <iframe src="{{ $mapSrc }}" width="100%" height="100%" class="contact-map-wrap__iframe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mercy Tides Foundation location map"></iframe>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/get-involved-form.js') }}" defer></script>
@endpush
