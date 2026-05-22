@php
    $aboutRow = $about ?? \App\Models\Background::firstOrEmpty();
    $metricItems = collect();

    if (($impacts ?? collect())->isNotEmpty()) {
        $metricItems = $impacts->take(4)->map(fn ($item) => [
            'value' => $item->value ?? '—',
            'label' => $item->title ?? '',
        ]);
    } else {
        $metricItems = collect([
            ['value' => $aboutRow->families_impacted ?? '500+', 'label' => 'Mothers empowered'],
            ['value' => $aboutRow->jobs_created ?? '200+', 'label' => 'Businesses launched'],
            ['value' => $aboutRow->training_hours ?? '50+', 'label' => 'Communities reached'],
            ['value' => '100%', 'label' => 'Christ-centered care'],
        ]);
    }

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
                    {{-- <span class="home-hero__eyebrow d-block mb-2">Measurable change</span> --}}
                    <h2 id="home-impact-heading" class="about-core-values-parallax__title mb-2">Our impact in Uganda</h2>
                    <p class="about-core-values-parallax__body mb-0">Lives transformed through vocational training, entrepreneurship, discipleship, and community care.</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center impact-stats-hero__grid">
                @foreach ($metricItems as $idx => $metric)
                    <div class="col-6 col-md-3 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay="{{ number_format(0.15 + ($idx * 0.1), 2) }}s">
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
