@extends('layouts.frontbase')

@section('title', 'Young Mothers We Support')

@section('content')
    @include('frontend.includes.page-header', [
        'title' => 'Young Mothers We Support',
        'pageKey' => 'gallery_mothers',
    ])

    <section class="mothers-gallery-page pt-90 pb-90">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <span class="about-home-eyebrow d-block mb-2">Faces of hope</span>
                    <p class="mothers-gallery-section__lead mx-auto mb-0">
                        Meet some of the courageous young mothers Mercy Tides walks alongside — each portrait is a story of resilience, faith, and new beginnings.
                    </p>
                </div>
            </div>

            @if(($mothers ?? collect())->isEmpty())
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0">Mother profiles will appear here once photos are published.</p>
                    </div>
                </div>
            @else
                <div class="row g-4 mothers-gallery-grid">
                    @foreach($mothers as $mother)
                        @php
                            $imageUrl = \App\Models\Mother::publicImageUrl($mother->image);
                            $hasDetails = $mother->hasProfileDetails();
                            $descriptionPlain = strip_tags(html_entity_decode($mother->description ?? ''));
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <article class="mother-portrait-card h-100">
                                @if($hasDetails)
                                    <a href="{{ $mother->profileRoute() }}" class="mother-portrait-card__media d-block" aria-label="View {{ $mother->displayName() }}'s story">
                                        <img src="{{ $imageUrl }}" alt="{{ $mother->displayName() }}" loading="lazy">
                                        <span class="mother-portrait-card__overlay">
                                            <span class="mother-portrait-card__cta">View story</span>
                                        </span>
                                    </a>
                                @else
                                    <div class="mother-portrait-card__media">
                                        <img src="{{ $imageUrl }}" alt="Young mother supported by Mercy Tides" loading="lazy">
                                    </div>
                                @endif

                                <div class="mother-portrait-card__body">
                                    @if(!empty($mother->name))
                                        <h3 class="mother-portrait-card__name">{{ $mother->name }}</h3>
                                    @endif
                                    @if(!empty($mother->age))
                                        <p class="mother-portrait-card__age">Age {{ $mother->age }}</p>
                                    @endif
                                    @if($descriptionPlain !== '')
                                        <p class="mother-portrait-card__excerpt">{{ \Illuminate\Support\Str::limit($descriptionPlain, 110, '…') }}</p>
                                    @endif
                                    @if($hasDetails)
                                        <a href="{{ $mother->profileRoute() }}" class="mother-portrait-card__link">
                                            Read her story <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
