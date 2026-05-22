@extends('layouts.frontbase')

@section('title', 'Programs & Core Values')

@section('content')

@php
    use App\Support\MercyTidesContent;
    $mission = $mission ?? \App\Models\About::firstOrEmpty();
    $programsHtml = MercyTidesContent::field($about->approach_content ?? null, MercyTidesContent::programOfferingsHtml() . MercyTidesContent::holisticLeadershipHtml());
    $coreValueItems = \App\Support\CoreValues::parseItems($mission->core_values_list ?? null, $mission->values ?? '');
    if (count($coreValueItems) === 0) {
        $coreValueItems = MercyTidesContent::coreValues();
    }
@endphp

@include('frontend.includes.page-header', [
    'title' => 'Programs & Core Values',
    'caption' => 'Vocational training, entrepreneurship, holistic leadership, and discipleship for mothers in Uganda.',
])

<section class="page-standalone grey-bg pt-60 pb-60">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-xl-10">
                <article class="page-standalone-card">
                    <header class="page-standalone-card__head">
                        <span class="page-standalone-card__icon"><i class="flaticon-mission"></i></span>
                        <div>
                            {{-- <p class="page-standalone-card__eyebrow">What we offer</p> --}}
                            <h2 class="page-standalone-card__title mb-0">Program offerings</h2>
                        </div>
                    </header>
                    <div class="page-standalone-card__body postbox__text">{!! $programsHtml !!}</div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="about-page-core-values pt-20 pb-90">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 text-center">
                <h4 class="tp-section-title mb-0">Our core values</h4>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($coreValueItems as $idx => $item)
                <div class="col-sm-6 col-lg-4">
                    <div class="about-core-white-card h-100">
                        <span class="about-core-white-card__index">{{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="mb-0">{{ $item }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
