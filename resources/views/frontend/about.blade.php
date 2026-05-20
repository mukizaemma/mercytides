@extends('layouts.frontbase')

@section('title', 'About Us')

@section('content')

@php use App\Support\MercyTidesContent; @endphp

@include('frontend.includes.page-header', [
    'title' => 'Our Story',
    'caption' => 'A Christian non-profit serving unwed young mothers in Uganda — the Pearl of Africa.',
])

<!-- Background section -->
<section class="about-page-intro pt-60 pb-60 grey-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10 col-xxl-9">
                <div class="tp-about-4__section-title mb-4">
                    <h4 class="tp-section-title">Overview</h4>
                </div>
                <div class="postbox__text about-page-body" style="font-size: 19px; line-height: 1.75; color: #333;">
                    {!! MercyTidesContent::field($about->description ?? null, MercyTidesContent::overview()) !!}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & vision band -->
@include('frontend.includes.programs-dual-cta', ['about' => $about, 'mission' => $mission])

@include('frontend.includes.objectives-grid', [
    'sectionClass' => 'mt-objectives--on-grey pt-60 pb-20',
    'caption' => 'How we serve unwed young mothers, their children, and partner communities in Uganda.',
])

<!-- Core values on white background -->
@php
    $coreValueItems = \App\Support\CoreValues::parseItems($mission->core_values_list ?? null, $mission->values ?? '');
    if (count($coreValueItems) === 0) {
        $coreValueItems = \App\Support\MercyTidesContent::coreValues();
    }
@endphp
<section class="about-page-core-values pt-80 pb-80">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 text-center">
                <h4 class="tp-section-title mb-0">Our Core Values</h4>
            </div>
        </div>
        @if(count($coreValueItems) > 0)
            <div class="row g-4 justify-content-center">
                @foreach($coreValueItems as $idx => $item)
                    <div class="col-sm-6 col-lg-4">
                        <div class="about-core-white-card h-100">
                            <span class="about-core-white-card__index">{{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="mb-0">{{ $item }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8 text-center">
                    <div class="postbox__text">{!! $mission->values ?? '' !!}</div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Team section -->
<section class="about-page-team pt-10 pb-90 grey-bg">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="tp-team-2__section-title pb-40 text-center">
                    <h4 class="tp-section-title">Foundation Leadership</h4>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @forelse($staff as $member)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <article class="tp-team-2__item text-center h-100">
                        <div class="tp-team-2__thumb">
                            <img src="{{ asset('storage/images/staff') . $member->image }}" alt="{{ $member->names }}">
                        </div>
                        <div class="tp-team-2__content">
                            <div class="tp-team-2__author-info">
                                <h4 class="tp-team-2__author-name">{{ $member->names }}</h4>
                                <span>{{ $member->position }}</span>
                            </div>
                        </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">Team members will appear here once published.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('frontend.includes.backImage')

@endsection
