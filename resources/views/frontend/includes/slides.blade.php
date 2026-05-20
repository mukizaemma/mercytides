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
                                            <p class="home-hero__eyebrow">Mercy Tides Foundation · Uganda</p>
                                            <h1 class="tp-slider-3-title pb-20 home-hero__title">
                                                {{ $slide->heading ?: 'Breaking Barriers, Bridging A Better Future' }}
                                            </h1>
                                            {{-- <p class="home-hero__lede">Hopeful, Christ-centered support for mothers and children through training, entrepreneurship, and community care.</p> --}}
                                            <div class="home-hero__actions d-flex flex-wrap justify-content-center gap-2 gap-md-3 pt-10">
                                                <a class="tp-btn home-hero__btn" href="{{ route('sponsorMother') }}">Sponsor a Mother</a>
                                                <a class="tp-btn home-hero__btn" href="{{ route('donate') }}">Donate Now</a>
                                                <a class="tp-btn home-hero__btn home-hero__btn--outline" href="{{ route('applyForSupport') }}">Apply for Support</a>
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
                                            <p class="home-hero__eyebrow">Mercy Tides Foundation · Uganda</p>
                                            <h1 class="tp-slider-3-title pb-20 home-hero__title">Breaking Barriers, Bridging A Better Future</h1>
                                            <p class="home-hero__lede">Add hero slides from the admin dashboard to showcase mothers, children, and community impact.</p>
                                            <div class="home-hero__actions d-flex flex-wrap justify-content-center gap-2 gap-md-3 pt-10">
                                                <a class="tp-btn home-hero__btn" href="{{ route('donate') }}">Donate Now</a>
                                                <a class="tp-btn home-hero__btn home-hero__btn--outline" href="{{ route('applyForSupport') }}">Apply for Support</a>
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
