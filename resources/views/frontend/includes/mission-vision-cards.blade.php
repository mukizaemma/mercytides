@php
    use App\Support\MercyTidesContent;

    $missionRow = $mission ?? \App\Models\About::firstOrEmpty();
    $missionText = strip_tags(html_entity_decode(
        MercyTidesContent::plain($missionRow->mission ?? null, strip_tags(MercyTidesContent::mission()))
    ));
    $visionText = strip_tags(html_entity_decode(
        MercyTidesContent::plain($missionRow->vision ?? null, 'Breaking Barriers, Bridging A Better Future')
    ));
@endphp

<div class="programs-dual-cta__row home-impact-unified__mv">
    <div class="row g-4 g-md-3 align-items-stretch justify-content-center">
        <div class="col-12 col-md-5 col-xl-5 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".15s">
            <article class="programs-dual-cta-card programs-dual-cta-card--primary">
                <span class="programs-dual-cta-card__heading">
                    <span class="programs-dual-cta-card__icon" aria-hidden="true">
                        <i class="fas fa-bullseye"></i>
                    </span>
                    <span class="programs-dual-cta-card__label">Mission</span>
                </span>
                <span class="programs-dual-cta-card__hint">{{ $missionText }}</span>
            </article>
        </div>
        <div class="col-md-2 d-none d-md-flex align-items-center justify-content-center programs-dual-cta__or-wrap">
            <span class="programs-dual-cta__or" aria-hidden="true">&amp;</span>
        </div>
        <div class="col-12 col-md-5 col-xl-5 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".25s">
            <article class="programs-dual-cta-card programs-dual-cta-card--secondary">
                <span class="programs-dual-cta-card__heading">
                    <span class="programs-dual-cta-card__icon" aria-hidden="true">
                        <i class="fas fa-eye"></i>
                    </span>
                    <span class="programs-dual-cta-card__label">Vision</span>
                </span>
                <span class="programs-dual-cta-card__hint">{{ $visionText }}</span>
            </article>
        </div>
    </div>
</div>
