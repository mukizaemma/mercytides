@php
    $aboutRow = $about ?? \App\Models\Background::firstOrEmpty();
    $metricItems = \App\Support\ImpactMetrics::forPublic();
    $metricColClass = \App\Support\ImpactMetrics::columnClass($metricItems->count());

    $statsBg = $aboutRow->image1 ?? $aboutRow->image ?? '';
    $statsBgUrl = $statsBg !== ''
        ? (str_starts_with((string) $statsBg, 'http') ? $statsBg : asset('storage/images/' . ltrim($statsBg, '/')))
        : '';
@endphp

<section class="impact-stats-hero {{ $statsBgUrl ? '' : 'impact-stats-hero--no-bg' }} home-impact-band home-impact-unified"
    @if($statsBgUrl) style="--impact-stats-bg: url('{{ $statsBgUrl }}');" @endif
    aria-labelledby="home-impact-heading">
    <div class="impact-stats-hero__overlay">
        <div class="container">
            @include('frontend.includes.mission-vision-cards', ['mission' => $mission ?? null])

            <div class="home-impact-unified__divider" aria-hidden="true"></div>

            <div class="row mb-4 home-impact-unified__stats-head">
                <div class="col-12 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                    <h2 id="home-impact-heading" class="about-core-values-parallax__title mb-2">Change you can measure in Uganda</h2>
                    <p class="about-core-values-parallax__body mb-0">Mothers equipped, families strengthened, and communities renewed through skills, discipleship, and Christ-centered care.</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center impact-stats-hero__grid">
                @foreach ($metricItems as $idx => $metric)
                    <div class="{{ $metricColClass }} wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay="{{ number_format(0.15 + ($idx * 0.1), 2) }}s">
                        <article class="impact-stats-hero__stat">
                            <p class="impact-stats-hero__value">{{ $metric['value'] }}</p>
                            <p class="impact-stats-hero__label">{{ $metric['label'] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
