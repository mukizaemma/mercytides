@php
    $galleryProfiles = collect($mothers ?? $profiles ?? [])->take($limit ?? 4);
    $showSectionHeader = $showSectionHeader ?? true;
    $embedded = $embedded ?? false;
    $sectionTitle = $sectionTitle ?? 'Mothers in our program';
    $sectionLead = $sectionLead ?? 'Meet some of the young mothers we walk with — toward independence, dignity, and a brighter future for their children.';
    $viewMoreRoute = $viewMoreRoute ?? route('sponsorship.youngMother');
    $viewMoreLabel = $viewMoreLabel ?? 'View more mothers';
    $showViewMore = $showViewMore ?? true;
@endphp

@if($galleryProfiles->isNotEmpty())
@if(!$embedded)
<section class="mothers-gallery-section tp-blog-2__area {{ $showSectionHeader ? 'tp-blog-2__spaces' : 'pb-0' }}" aria-labelledby="mothers-gallery-heading">
    <div class="container">
@endif
        @if($showSectionHeader)
        <div class="row">
            <div class="col-xl-12 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                <div class="mothers-gallery-section__header pb-50 text-center">
                    <h2 id="mothers-gallery-heading" class="tp-section-title">{{ $sectionTitle }}</h2>
                    @if(!empty($sectionLead))
                        <p class="mothers-gallery-section__lead mx-auto mb-0">{{ $sectionLead }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4 justify-content-center">
            @foreach ($galleryProfiles as $profile)
                <div class="col-6 col-md-4 col-lg-3 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".15s">
                    @include('frontend.includes.sponsorship-card', ['profile' => $profile])
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
