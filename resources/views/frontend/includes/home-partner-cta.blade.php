<section class="home-partner-cta" aria-labelledby="home-partner-cta-heading">
    <div class="container home-partner-cta__inner">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7 reveal-on-scroll" data-reveal-delay="0">
                <h2 id="home-partner-cta-heading" class="home-partner-cta__title">
                    {{ \App\Support\SiteContent::text('home.partner.title') }}
                </h2>
                <p class="home-partner-cta__text mb-0">
                    {{ \App\Support\SiteContent::text('home.partner.text') }}
                </p>
            </div>
            <div class="col-lg-5 reveal-on-scroll" data-reveal-delay="120">
                <div class="home-partner-cta__stats">
                    @foreach ([1, 2, 3] as $partnerStat)
                        <div class="home-partner-cta__stat">
                            <strong>{{ \App\Support\SiteContent::text('home.partner.stat'.$partnerStat.'_title') }}</strong>
                            <span>{{ \App\Support\SiteContent::text('home.partner.stat'.$partnerStat.'_text') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('getInvolved') }}" class="tp-btn">{{ \App\Support\SiteContent::text('home.partner.btn_involved') }}</a>
                    <a href="{{ route('sponsorship.hub') }}" class="tp-btn home-partner-cta__btn--ghost">{{ \App\Support\SiteContent::text('home.partner.btn_sponsorship') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
