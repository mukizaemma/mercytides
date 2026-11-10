@extends('layouts.frontbase')

@section('title', \App\Support\SiteContent::text('mission.page_title'))

@section('content')

@php
    use App\Support\MercyTidesContent;
    $missionHtml = MercyTidesContent::field($mission->mission ?? null, MercyTidesContent::mission());
    $visionHtml = MercyTidesContent::field($mission->vision ?? null, MercyTidesContent::vision());
@endphp

@include('frontend.includes.page-header', [
    'title' => \App\Support\SiteContent::text('mission.page_title'),
    'caption' => \App\Support\SiteContent::text('mission.page_caption'),
    'pageKey' => 'mission',
])

<section class="tp-about-4__area tp-about-4__space p-relative fix grey-bg pt-60 pb-60">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-xl-6 col-lg-6 wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".1s">
                <article class="page-standalone-card h-100">
                    <header class="page-standalone-card__head">
                        <span class="page-standalone-card__icon"><i class="flaticon-mission"></i></span>
                        <div>
                            <h2 class="page-standalone-card__title mb-0">{{ \App\Support\SiteContent::text('mission.card_mission') }}</h2>
                        </div>
                    </header>
                    <div class="page-standalone-card__body postbox__text">{!! $missionHtml !!}</div>
                </article>
            </div>
            <div class="col-xl-6 col-lg-6 wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".2s">
                <article class="page-standalone-card h-100">
                    <header class="page-standalone-card__head">
                        <span class="page-standalone-card__icon"><i class="flaticon-vision"></i></span>
                        <div>
                            <h2 class="page-standalone-card__title mb-0">{{ \App\Support\SiteContent::text('mission.card_vision') }}</h2>
                        </div>
                    </header>
                    <div class="page-standalone-card__body postbox__text">{!! $visionHtml !!}</div>
                </article>
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.objectives-grid')

@include('frontend.includes.core-values-parallax', [
    'coreValuesParallaxTitle' => \App\Support\SiteContent::text('about.core_values_title'),
])

@endsection
