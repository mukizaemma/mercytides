@extends('layouts.frontbase')

@section('content')

@php use App\Support\MercyTidesContent; @endphp

    {{-- 1. Promise --}}
    @include('frontend.includes.slides')

    {{-- 2. The gap & our response --}}
    <section class="home-problem-solution-section home-narrative-band" aria-labelledby="home-gap-heading">
        <div class="container">
            <div class="row justify-content-center mb-4 mb-lg-5">
                <div class="col-lg-9 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                    <h2 id="home-gap-heading" class="tp-section-title mb-3">Barriers young mothers face — and how we respond</h2>
                    <p class="home-narrative-band__lead mb-0">
                        Across communities near Kampala, unwed and teenage mothers often lack skills, safety, and hope.
                        Mercy Tides walks with them toward dignity, livelihood, and Christ-centered care.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".15s">
                    <article class="about-home-pillar about-home-pillar--gap h-100">
                        <div class="about-home-pillar__head">
                            <span class="about-home-pillar__icon" aria-hidden="true"><i class="fas fa-triangle-exclamation"></i></span>
                            <div>
                                <h3 class="about-home-pillar__title">Why mothers need support</h3>
                            </div>
                        </div>
                        <p class="about-home-pillar__text mb-0">
                            {{ Str::limit(strip_tags(html_entity_decode($about->problem_statement ?? MercyTidesContent::problemStatement())), 420, '…') }}
                        </p>
                    </article>
                </div>
                <div class="col-lg-6 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".25s">
                    <article class="about-home-pillar about-home-pillar--solution h-100">
                        <div class="about-home-pillar__head">
                            <span class="about-home-pillar__icon" aria-hidden="true"><i class="fas fa-hands-helping"></i></span>
                            <div>
                                <h3 class="about-home-pillar__title">Skills, enterprise &amp; care</h3>
                            </div>
                        </div>
                        <p class="about-home-pillar__text mb-0">
                            {{ Str::limit(strip_tags(html_entity_decode($about->solution_statement ?? MercyTidesContent::solutionStatement())), 420, '…') }}
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Who we are --}}
    <section class="tp-about-4__area p-relative fix grey-bg about-home-section" aria-labelledby="home-about-heading">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".2s">
                    <div class="about-home-intro about-home-intro--centered">
                        <h2 id="home-about-heading" class="tp-section-title about-home-title">About Mercy Tides Foundation</h2>
                        @php
                            $aboutDescRaw = $about->description ?? '';
                            $aboutDescPlain = strip_tags(html_entity_decode($aboutDescRaw));
                            $words = Str::limit($aboutDescPlain !== '' ? $aboutDescPlain : strip_tags(MercyTidesContent::overview()), 380, '...');
                        @endphp
                        <p class="about-home-lead mx-auto">{{ $words }}</p>
                        <div class="about-home-actions justify-content-center">
                            <a class="tp-btn about-home-cta" href="{{ route('backgroundDetails') }}">
                                Our story <span class="about-home-cta-arrow" aria-hidden="true">→</span>
                            </a>
                            <a class="tp-btn tp-btn--outline about-home-cta-secondary" href="{{ route('ourModel') }}">
                                Where we work
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. How we serve --}}
    @include('frontend.includes.services')

    {{-- 5. Impact --}}
    @include('frontend.includes.home-impact-metrics')

    {{-- 6. Ways to get involved --}}
    @include('frontend.includes.home-partner-cta')

    {{-- 7. How we work day to day --}}
    @include('frontend.includes.home-mission-core-values')

    @include('frontend.includes.home-news')

    @if(($setting->show_products_publicly ?? false))
        @include('frontend.includes.home-products')
    @endif

    {{-- Mothers in our program — just above site-wide Partners --}}
    @include('frontend.includes.mothers-gallery', [
        'mothers' => $mothers ?? collect(),
        'limit' => 4,
        'sectionTitle' => 'Mothers in our program',
        'sectionLead' => 'Meet some of the young mothers we walk with — toward independence, dignity, and a brighter future for their children.',
        'viewMoreRoute' => route('sponsorship.youngMother'),
        'viewMoreLabel' => 'View more mothers',
    ])

@endsection
