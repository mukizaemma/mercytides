@extends('layouts.frontbase')

@section('title', \App\Support\SiteContent::text('sponsorship.hub_title_page'))

@php
    use App\Support\MercyTidesContent;

    $hubCards = MercyTidesContent::sponsorshipHubCards();
@endphp

@section('content')

@include('frontend.includes.page-header', [
    'title' => \App\Support\SiteContent::text('sponsorship.hub_title_page'),
    'caption' => \App\Support\SiteContent::text('sponsorship.hub_caption'),
    'pageKey' => 'sponsorship_hub',
])

<section class="sponsorship-hub-page pt-90 pb-90 grey-bg">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-lg-9 text-center">
                <h2 class="h3 mb-3">{{ \App\Support\SiteContent::text('sponsorship.hub_title') }}</h2>
                <p class="text-muted mb-0">{{ \App\Support\SiteContent::text('sponsorship.hub_lead') }}</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center sponsorship-hub-cards">
            @foreach($hubCards as $card)
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route($card['route']) }}" class="sponsorship-hub-card h-100 text-decoration-none">
                        <span class="sponsorship-hub-card__icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
                        <h3 class="sponsorship-hub-card__title">{{ $card['title'] }}</h3>
                        <p class="sponsorship-hub-card__text mb-0">{{ $card['text'] }}</p>
                        <span class="sponsorship-hub-card__cta">Learn more <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
