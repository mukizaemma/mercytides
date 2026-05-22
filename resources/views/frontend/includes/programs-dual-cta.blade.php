@php
    $ctaBgRow = $about ?? \App\Models\Background::firstOrEmpty();
    $missionRow = $mission ?? \App\Models\About::firstOrEmpty();
    /* Prefer a dedicated hero slot so this band differs from the impact strip (often image1). */
    $ctaFile = $ctaBgRow->core_values_background ?? $ctaBgRow->image2 ?? $ctaBgRow->image ?? $ctaBgRow->image1 ?? '';
    $ctaBgUrl = $ctaFile !== ''
        ? (str_starts_with((string) $ctaFile, 'http')
            ? $ctaFile
            : asset('storage/images/' . ltrim($ctaFile, '/')))
        : '';
@endphp

<section class="programs-dual-cta {{ $ctaBgUrl ? '' : 'programs-dual-cta--no-bg' }}"
    @if($ctaBgUrl)
        style="--programs-cta-bg: url('{{ $ctaBgUrl }}'); min-height: 100vh; background-attachment: fixed;"
    @endif
    aria-labelledby="programs-dual-cta-heading"
>
    <div class="programs-dual-cta__overlay">
        <div class="programs-dual-cta__accent" aria-hidden="true"></div>
        <div class="programs-dual-cta__grain" aria-hidden="true"></div>
        <div class="container position-relative">
            @include('frontend.includes.mission-vision-cards', ['mission' => $missionRow])
        </div>
    </div>
</section>
<style>
    .programs-dual-cta {
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    @media (max-width: 991.98px) {
        .programs-dual-cta {
            min-height: auto;
            background-attachment: scroll !important;
        }
    }
</style>
