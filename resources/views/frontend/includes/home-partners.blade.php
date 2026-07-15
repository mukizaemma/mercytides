@if(($partners ?? collect())->isNotEmpty())
<section class="home-partners-section" aria-labelledby="our-partners-heading">
    <div class="container">
        <div class="home-partners-section__header">
            <h2 id="our-partners-heading" class="home-partners-section__title">Our Partners</h2>
        </div>

        <div class="home-partners-section__grid" role="list">
            @foreach ($partners as $partner)
                <div class="home-partners-section__item" role="listitem">
                    @if($partner->hasLogo())
                        @if(!empty($partner->website))
                            <a
                                href="{{ \Illuminate\Support\Str::startsWith($partner->website, ['http://', 'https://']) ? $partner->website : 'https://'.$partner->website }}"
                                class="home-partners-section__link"
                                target="_blank"
                                rel="noopener"
                                aria-label="{{ $partner->names ?? 'Partner' }}"
                            >
                                <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->names ?? 'Partner' }}" class="home-partners-section__logo" loading="lazy">
                            </a>
                        @else
                            <span class="home-partners-section__link">
                                <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->names ?? 'Partner' }}" class="home-partners-section__logo" loading="lazy">
                            </span>
                        @endif
                    @else
                        <span class="home-partners-section__name">{{ $partner->names ?? 'Partner' }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
