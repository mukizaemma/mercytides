@extends('layouts.frontbase')

@section('title', 'Leadership Team')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Foundation Leadership',
    'caption' => 'Mercy Tides Foundation is led by a committed team serving mothers and families in Uganda.',
    'pageKey' => 'team',
])

<section class="about-page-team pt-60 pb-90 grey-bg">
    <div class="container">
        <div class="row g-4">
            @forelse($team as $rs)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <article class="tp-team-2__item team-card text-center h-100 bg-white rounded-3 shadow-sm">
                        @include('frontend.includes.team-avatar', [
                            'src' => !empty($rs->image) ? \App\Support\StorageImage::url($rs->image, 'images/staff') : null,
                            'alt' => $rs->names,
                            'focus' => $rs->imageFocusCss(),
                        ])
                        <div class="team-card__identity tp-team-2__content p-0">
                            <h4 class="tp-team-2__author-name h5">{{ $rs->names }}</h4>
                            <span class="d-block text-muted">{{ $rs->position }}</span>
                        </div>
                        @if(!empty($rs->bio))
                            <div class="team-card__bio postbox__text text-start small">{!! $rs->bio !!}</div>
                        @endif
                    </article>
                </div>
            @empty
                @foreach(\App\Support\MercyTidesContent::leadershipTeam() as $leader)
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <article class="tp-team-2__item team-card text-center h-100 bg-white rounded-3 shadow-sm">
                            @include('frontend.includes.team-avatar', [
                                'alt' => $leader['names'],
                            ])
                            <div class="team-card__identity">
                                <h4 class="h5 mb-1">{{ $leader['names'] }}</h4>
                                <span class="d-block fw-semibold" style="color:var(--brand-primary,#3386B5);">{{ $leader['position'] }}</span>
                            </div>
                            <div class="team-card__bio postbox__text text-start small">{!! $leader['bio'] !!}</div>
                        </article>
                    </div>
                @endforeach
            @endforelse
        </div>

        @if($advisors->isNotEmpty())
            <div class="row mt-5 pt-5">
                <div class="col-12 text-center pb-40">
                    <h4 class="tp-section-title">Advisors</h4>
                </div>
            </div>
            <div class="row g-4">
                @foreach ($advisors as $adv)
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <article class="tp-team-2__item team-card text-center h-100 bg-white rounded-3 shadow-sm">
                            @include('frontend.includes.team-avatar', [
                                'src' => !empty($adv->image) ? \App\Support\StorageImage::url($adv->image, 'images/staff') : null,
                                'alt' => $adv->names,
                                'focus' => $adv->imageFocusCss(),
                                'size' => 'sm',
                            ])
                            <div class="team-card__identity">
                                <h4 class="h6 mb-1">{{ $adv->names }}</h4>
                                <span class="text-muted small">{{ $adv->position }}</span>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('frontend.includes.backImage')

@endsection
