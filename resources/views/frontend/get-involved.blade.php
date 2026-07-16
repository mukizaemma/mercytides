@extends('layouts.frontbase')

@section('title', 'Get Involved')

@php
    use App\Support\MercyTidesContent;
    use Illuminate\Support\Str;

    $ways = MercyTidesContent::getInvolvedWays();
    $selectedWays = (array) old('ways', []);
    $preselect = request('way');
    if ($preselect && array_key_exists($preselect, $ways) && $selectedWays === []) {
        $selectedWays = [$preselect];
    }

    $introFull = $introHtml ?? MercyTidesContent::getInvolvedWhy();
    $introPlain = strip_tags(html_entity_decode($introFull));
    preg_match('/<p\b[^>]*>.*?<\/p>/is', $introFull, $introMatch);
    $introTeaserHtml = $introMatch[0] ?? '<p>' . e(Str::limit($introPlain, 280, '…')) . '</p>';
    $hasMoreIntro = strlen($introPlain) > strlen(strip_tags(html_entity_decode($introTeaserHtml))) + 40;
@endphp

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Get Involved',
    'caption' => 'Stand with young mothers in Uganda — choose a clear way to support her journey.',
    'pageKey' => 'get_involved',
])

<section class="get-involved-page pt-90 pb-90 grey-bg">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-lg-10">
                <div class="get-involved-page__intro card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h3 mb-3">Your partnership changes lives</h2>
                        <div class="get-involved-page__intro-teaser postbox__text">{!! $introTeaserHtml !!}</div>
                        @if($hasMoreIntro)
                            <div class="collapse get-involved-page__intro-more" id="getInvolvedIntroMore">
                                <div class="postbox__text get-involved-page__intro-text mt-3">{!! $introFull !!}</div>
                            </div>
                            <button type="button"
                                    class="btn btn-link get-involved-page__read-more px-0 mt-2"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#getInvolvedIntroMore"
                                    aria-expanded="false"
                                    aria-controls="getInvolvedIntroMore">
                                Read full story <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                            </button>
                        @else
                            <div class="postbox__text get-involved-page__intro-text mt-2">{!! $introFull !!}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(($impactStats ?? collect())->isNotEmpty())
            @php
                $giStatsCol = \App\Support\ImpactMetrics::columnClass(($impactStats ?? collect())->count());
            @endphp
            <div class="row g-3 g-md-4 mb-4 mb-lg-5 justify-content-center get-involved-stats">
                @foreach($impactStats as $stat)
                    <div class="{{ $giStatsCol }}">
                        <div class="get-involved-stat text-center h-100">
                            <p class="get-involved-stat__value mb-1">{{ $stat['value'] }}</p>
                            <p class="get-involved-stat__label mb-0">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mb-4 mb-lg-5">
            <a href="#get-involved-form" class="tp-btn get-involved-scroll-cta js-smooth-scroll">
                Start your request <i class="fas fa-arrow-down ms-2" aria-hidden="true"></i>
            </a>
        </div>

        <div class="row justify-content-center" id="get-involved-form">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm site-form-card get-involved-page__form-card">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 mb-2">Choose a way to support mothers</h2>
                        <p class="text-muted mb-4">These are the same support paths used across Mercy Tides. Pick one, share your details, then send via WhatsApp or email.</p>

                        @include('frontend.includes.get-involved-form', [
                            'singleSelect' => true,
                            'formPrefix' => 'gi',
                            'countriesListId' => 'get-involved-countries',
                            'selectedWays' => $selectedWays,
                            'waysLabel' => 'Ways to support mothers',
                            'waysHint' => 'Choose the option that matches how you want to help.',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/get-involved-form.js') }}" defer></script>
@endpush
