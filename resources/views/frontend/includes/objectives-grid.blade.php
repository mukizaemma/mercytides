@php
    $objectives = $objectives ?? \App\Support\MercyTidesContent::objectives();
    $icons = \App\Support\MercyTidesContent::objectiveIcons();
    $caption = $caption ?? 'How we serve unwed young mothers, their children, and partner communities in Uganda.';
    $sectionClass = trim('mt-objectives ' . ($sectionClass ?? ''));
@endphp

<section class="{{ $sectionClass }}">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-12 col-lg-10 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                <span class="mt-objectives__eyebrow">How we work</span>
                <h4 class="tp-section-title mb-3">What guides our work every day</h4>
                @if(!empty($caption))
                    <p class="mt-objectives__lead mb-0">{{ $caption }}</p>
                @endif
            </div>
        </div>
        <div class="row g-4 g-xl-4 justify-content-center mt-objectives__grid">
            @foreach ($objectives as $idx => $item)
                @php
                    $number = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
                    $icon = $icons[$idx] ?? 'fas fa-check-circle';
                    $delay = number_format(0.1 + ($idx * 0.08), 2, '.', '');
                @endphp
                <div class="col-sm-6 col-lg-4">
                    <article class="mt-objective-card h-100 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay="{{ $delay }}s">
                        <span class="mt-objective-card__watermark" aria-hidden="true">{{ $number }}</span>
                        <div class="mt-objective-card__top">
                            <span class="mt-objective-card__icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                            <span class="mt-objective-card__index">{{ $number }}</span>
                        </div>
                        <p class="mt-objective-card__text mb-0">{{ $item }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
