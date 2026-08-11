@extends('layouts.frontbase')

@php
    use App\Support\MercyTidesContent;
    $meta = MercyTidesContent::foundingStoryMeta();
    $pageTitle = MercyTidesContent::plain($story->title ?? null, $meta['title']);
    $tagline = MercyTidesContent::plain($story->tagline ?? null, $meta['tagline']);
    $caption = MercyTidesContent::plain($story->header_caption ?? null, $meta['header_caption']);
    $founderName = MercyTidesContent::plain($story->founder_name ?? null, $meta['founder_name']);
    $founderRole = MercyTidesContent::plain($story->founder_role ?? null, $meta['founder_role']);
    $bodyHtml = MercyTidesContent::field($story->content ?? null, MercyTidesContent::foundingStoryHtml());
    $founderImageUrl = \App\Support\StorageImage::url($story->founder_image ?? null, 'images/founder');
@endphp

@section('title', $pageTitle)

@section('content')

@include('frontend.includes.page-header', [
    'title' => $pageTitle,
    'caption' => $caption,
    'pageKey' => 'founding_story',
])

<section class="founder-story grey-bg pt-60 pb-90">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-start">
            <div class="col-lg-4">
                <aside class="founder-story__aside">
                    <figure class="founder-story__portrait mb-0">
                        @if($founderImageUrl)
                            <div class="founder-story__frame">
                                <img src="{{ $founderImageUrl }}" alt="{{ $founderName }}" class="founder-story__img">
                            </div>
                        @else
                            <div class="founder-story__placeholder">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <p class="mb-0">Founder portrait coming soon</p>
                            </div>
                        @endif
                        <figcaption class="founder-story__caption">
                            <h2 class="founder-story__name">{{ $founderName }}</h2>
                            <p class="founder-story__role mb-0">{{ $founderRole }}</p>
                        </figcaption>
                    </figure>
                </aside>
            </div>
            <div class="col-lg-8">
                <article class="page-standalone-card founder-story__card">
                    <header class="page-standalone-card__head">
                        <span class="page-standalone-card__icon" aria-hidden="true"><i class="fas fa-book-open"></i></span>
                        <div>
                            <p class="page-standalone-card__eyebrow mb-1">The story behind Mercy Tides</p>
                            <h2 class="page-standalone-card__title mb-0">{{ $tagline }}</h2>
                        </div>
                    </header>
                    <div class="page-standalone-card__body postbox__text founder-story__body">
                        {!! $bodyHtml !!}
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

@endsection
