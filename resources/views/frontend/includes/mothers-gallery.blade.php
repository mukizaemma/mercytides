@php
    $galleryMothers = ($mothers ?? collect())->take($limit ?? 4);
    $showSectionHeader = $showSectionHeader ?? true;
    $embedded = $embedded ?? false;
    $sectionEyebrow = $sectionEyebrow ?? 'Faces of hope';
    $sectionTitle = $sectionTitle ?? 'Young Mothers We Support';
    $sectionLead = $sectionLead ?? 'Each portrait represents a young mother walking toward independence, dignity, and a brighter future for her family.';
    $viewMoreRoute = $viewMoreRoute ?? route('mothersGallery');
    $viewMoreLabel = $viewMoreLabel ?? 'View more mothers';
    $showViewMore = $showViewMore ?? true;
@endphp

@if($galleryMothers->isNotEmpty())
@if(!$embedded)
<section class="mothers-gallery-section tp-blog-2__area {{ $showSectionHeader ? 'tp-blog-2__spaces' : 'pb-0' }}">
    <div class="container">
@endif
        @if($showSectionHeader)
        <div class="row">
            <div class="col-xl-12 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                <div class="mothers-gallery-section__header pb-50 text-center">
                    <span class="about-home-eyebrow d-block mb-2">{{ $sectionEyebrow }}</span>
                    <h4 class="tp-section-title">{{ $sectionTitle }}</h4>
                    @if(!empty($sectionLead))
                        <p class="mothers-gallery-section__lead mx-auto mb-0">{{ $sectionLead }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4 mothers-gallery-grid justify-content-center">
            @foreach ($galleryMothers as $mother)
                @php
                    $imageUrl = $mother instanceof \App\Models\Sponsorship
                        ? \App\Models\Sponsorship::publicImageUrl($mother->image)
                        : \App\Models\Mother::publicImageUrl($mother->image);
                    $hasDetails = $mother->hasProfileDetails();
                    $descriptionPlain = strip_tags(html_entity_decode($mother->testimany ?? $mother->description ?? ''));
                    $displayName = method_exists($mother, 'displayName') ? $mother->displayName() : ($mother->name ?? 'Young mother');
                @endphp
                <div class="col-6 col-md-4 col-lg-3 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".15s">
                    <article class="mother-portrait-card h-100">
                        @if($hasDetails)
                            <a href="{{ $mother->profileRoute() }}" class="mother-portrait-card__media d-block" aria-label="View {{ $displayName }}'s story">
                                <img src="{{ $imageUrl }}" alt="{{ $displayName }}" loading="lazy">
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
                            @if(!empty($mother->names) || !empty($mother->name))
                                <h3 class="mother-portrait-card__name">{{ $mother->names ?? $mother->name }}</h3>
                            @endif
                            @if(!empty($mother->age))
                                <p class="mother-portrait-card__age">Age {{ $mother->age }}</p>
                            @endif
                            @if($descriptionPlain !== '')
                                <p class="mother-portrait-card__excerpt">{{ \Illuminate\Support\Str::limit($descriptionPlain, 90, '…') }}</p>
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

        @if($showViewMore)
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a class="tp-btn mothers-gallery-section__more-btn" href="{{ $viewMoreRoute }}">{{ $viewMoreLabel }}</a>
            </div>
        </div>
        @endif
@if(!$embedded)
    </div>
</section>
@endif
@endif
