<div class="tp-slider-3__area p-relative custom-banners home-hero">
    <div class="tp-slider-3__wrapper">
        <div class="swiper-container tp-slider-3__active">
            <div class="swiper-wrapper">
                @forelse ($slides as $slide)
                    <div class="swiper-slide">
                        <div class="tp-slider-3__bg z-index fix p-relative" data-background="{{ \App\Models\Slide::publicImageUrl($slide->image) }}">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="tp-slider-3__content text-center home-hero__content">
                                            <h1 class="tp-slider-3-title pb-20 home-hero__title">
                                                {{ $slide->heading ?: \App\Support\SiteContent::text('home.hero.title') }}
                                            </h1>
                                            @php
                                                $slideLede = trim((string) ($slide->subheading ?? ''));
                                                if ($slideLede === '' || strcasecmp($slideLede, 'Mercy Tides') === 0) {
                                                    $slideLede = \App\Support\SiteContent::text('home.hero.lede');
                                                }
                                            @endphp
                                            <p class="home-hero__lede">{{ $slideLede }}</p>
                                            <div class="home-hero__actions d-flex flex-wrap justify-content-center gap-2 gap-md-3 pt-10">
                                                <a class="tp-btn home-hero__btn" href="{{ route('getInvolved') }}">{{ \App\Support\SiteContent::text('home.hero.btn_involved') }}</a>
                                                <a class="tp-btn home-hero__btn home-hero__btn--outline" href="{{ route('sponsorship.hub') }}">{{ \App\Support\SiteContent::text('home.hero.btn_sponsorship') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide">
                        <div class="tp-slider-3__bg z-index fix p-relative grey-bg" style="min-height: 520px;">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="tp-slider-3__content text-center home-hero__content py-5">
                                            <h1 class="tp-slider-3-title pb-20 home-hero__title">{{ \App\Support\SiteContent::text('home.hero.title') }}</h1>
                                            <p class="home-hero__lede">{{ \App\Support\SiteContent::text('home.hero.empty_lede') }}</p>
                                            <div class="home-hero__actions d-flex flex-wrap justify-content-center gap-2 gap-md-3 pt-10">
                                                <a class="tp-btn home-hero__btn" href="{{ route('getInvolved') }}">{{ \App\Support\SiteContent::text('home.hero.btn_involved') }}</a>
                                                <a class="tp-btn home-hero__btn home-hero__btn--outline" href="{{ route('sponsorship.hub') }}">{{ \App\Support\SiteContent::text('home.hero.btn_sponsorship') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
