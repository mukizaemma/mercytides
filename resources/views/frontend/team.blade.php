@extends('layouts.frontbase')

@section('title', 'Leadership Team')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Foundation Leadership',
    'caption' => 'Mercy Tides Foundation is led by a committed team serving mothers and families in Uganda.',
])

<section class="about-page-team pt-60 pb-90 grey-bg">
    <div class="container">
        <div class="row g-4">
            @forelse($team as $rs)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <article class="tp-team-2__item text-center h-100 bg-white rounded-3 shadow-sm p-3">
                        @if(!empty($rs->image))
                            <div class="tp-team-2__thumb mb-3">
                                <img src="{{ asset('storage/images/staff') . $rs->image }}" alt="{{ $rs->names }}" class="rounded-circle" style="width:140px;height:140px;object-fit:cover;">
                            </div>
                        @else
                            <div class="tp-team-2__thumb mb-3 d-flex align-items-center justify-content-center rounded-circle mx-auto bg-light" style="width:140px;height:140px;">
                                <i class="fas fa-user fa-2x text-muted"></i>
                            </div>
                        @endif
                        <div class="tp-team-2__content">
                            <h4 class="tp-team-2__author-name h5">{{ $rs->names }}</h4>
                            <span class="d-block text-muted mb-2">{{ $rs->position }}</span>
                            @if(!empty($rs->bio))
                                <div class="postbox__text text-start small">{!! $rs->bio !!}</div>
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                @foreach(\App\Support\MercyTidesContent::leadershipTeam() as $leader)
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <article class="tp-team-2__item text-center h-100 bg-white rounded-3 shadow-sm p-4">
                            <div class="tp-team-2__thumb mb-3 d-flex align-items-center justify-content-center rounded-circle mx-auto" style="width:140px;height:140px;background:rgba(250,210,0,0.2);">
                                <i class="fas fa-user fa-2x text-dark"></i>
                            </div>
                            <h4 class="h5 mb-1">{{ $leader['names'] }}</h4>
                            <span class="d-block fw-semibold mb-2" style="color:var(--brand-primary,#fad200);">{{ $leader['position'] }}</span>
                            <div class="postbox__text text-start small">{!! $leader['bio'] !!}</div>
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
                        <article class="tp-team-2__item text-center h-100 bg-white rounded-3 shadow-sm p-3">
                            @if(!empty($adv->image))
                                <div class="tp-team-2__thumb mb-3">
                                    <img src="{{ asset('storage/images/staff') . $adv->image }}" alt="{{ $adv->names }}" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                </div>
                            @endif
                            <h4 class="h6 mb-1">{{ $adv->names }}</h4>
                            <span class="text-muted small">{{ $adv->position }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('frontend.includes.backImage')

@endsection
