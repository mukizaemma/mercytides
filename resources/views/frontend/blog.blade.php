@extends('layouts.frontbase')

@section('content')

@php
    $cover = $blog->image ? ltrim((string) $blog->image, '/') : '';
    if ($cover !== '' && ! str_contains($cover, '/')) {
        $cover = 'images/news/'.$cover;
    }
    $coverUrl = $cover !== '' ? asset('storage/'.$cover) : null;
    $galleryItems = ($images ?? collect())->filter(fn ($img) => ! empty($img->gallery))->values();
@endphp

<section class="postbox__area pt-120 pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-8 col-xl-8 col-lg-8">
                <div class="postbox__wrapper">
                    <article class="postbox__item format-image mb-40 transition-3">
                        @if($coverUrl)
                            <div class="postbox__thumb p-relative m-img update-cover">
                                <img src="{{ $coverUrl }}" alt="{{ $blog->title }}">
                            </div>
                        @endif
                        <div class="postbox__content">
                            <div class="postbox__meta mb-15">
                                <span>{{ optional($blog->created_at)->format('d M Y') }}</span>
                                @if(!empty($blog->author))
                                    <span>{{ $blog->author }}</span>
                                @endif
                            </div>
                            <h1 class="postbox__title">{{ $blog->title }}</h1>
                            <div class="postbox__text update-story">
                                {!! $blog->body !!}
                            </div>
                        </div>
                    </article>

                    @if($galleryItems->isNotEmpty())
                        <div class="update-gallery mt-40 mb-50">
                            <h2 class="update-gallery__title">Moments from this update</h2>
                            <p class="update-gallery__lead">Photos from the activity and community.</p>
                            <div class="row g-3" id="siteGalleryGrid">
                                @foreach ($galleryItems as $index => $image)
                                    @php
                                        $gPath = ltrim((string) $image->gallery, '/');
                                        $gUrl = asset('storage/'.$gPath);
                                    @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <button
                                            type="button"
                                            class="site-gallery__tile update-gallery__tile"
                                            data-gallery-open
                                            data-index="{{ $index }}"
                                            data-type="image"
                                            data-src="{{ $gUrl }}"
                                            data-caption="{{ e($image->caption ?? $blog->title) }}"
                                            aria-label="View photo{{ $image->caption ? ': '.$image->caption : '' }}"
                                        >
                                            <span class="site-gallery__media">
                                                <img src="{{ $gUrl }}" alt="{{ $image->caption ?: $blog->title }}" loading="lazy">
                                                <span class="site-gallery__zoom" aria-hidden="true">
                                                    <i class="far fa-expand"></i>
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="gallery-lightbox" id="galleryLightbox" hidden aria-hidden="true">
                            <div class="gallery-lightbox__backdrop" data-gallery-close></div>
                            <div class="gallery-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Photo viewer">
                                <button type="button" class="gallery-lightbox__close" data-gallery-close aria-label="Close">
                                    <i class="fal fa-times"></i>
                                </button>
                                <button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--prev" data-gallery-prev aria-label="Previous">
                                    <i class="fal fa-chevron-left"></i>
                                </button>
                                <button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--next" data-gallery-next aria-label="Next">
                                    <i class="fal fa-chevron-right"></i>
                                </button>
                                <div class="gallery-lightbox__stage">
                                    <img class="gallery-lightbox__image" id="galleryLightboxImage" alt="" hidden>
                                    <div class="gallery-lightbox__video-wrap" id="galleryLightboxVideoWrap" hidden>
                                        <iframe
                                            id="galleryLightboxFrame"
                                            title="Video"
                                            src=""
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                        ></iframe>
                                    </div>
                                </div>
                                <p class="gallery-lightbox__caption" id="galleryLightboxCaption" hidden></p>
                                <div class="gallery-lightbox__counter" id="galleryLightboxCounter" aria-live="polite"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xxl-4 col-xl-4 col-lg-4">
                <div class="sidebar__wrapper">
                    <div class="sidebar__widget mb-30">
                        <h3 class="sidebar__widget-title">More updates</h3>
                        <div class="sidebar__widget-content">
                            <div class="sidebar__post">
                                @forelse ($relatedBlogs as $rs)
                                    @php
                                        $relCover = $rs->image ? ltrim((string) $rs->image, '/') : '';
                                        if ($relCover !== '' && ! str_contains($relCover, '/')) {
                                            $relCover = 'images/news/'.$relCover;
                                        }
                                        $relUrl = $relCover !== '' ? asset('storage/'.$relCover) : null;
                                    @endphp
                                    <div class="rc__post mb-10 d-flex align-items-center">
                                        @if($relUrl)
                                            <div class="rc__post-thumb mr-20">
                                                <a href="{{ route('postSingle', $rs->slug) }}">
                                                    <img src="{{ $relUrl }}" alt="{{ $rs->title }}" width="90">
                                                </a>
                                            </div>
                                        @endif
                                        <div class="rc__post-content">
                                            <h3 class="rc__post-title">
                                                <a href="{{ route('postSingle', $rs->slug) }}">{{ $rs->title }}</a>
                                            </h3>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">More updates will appear here.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
    @if($galleryItems->isNotEmpty())
        <script src="{{ asset('assets/js/site-gallery-lightbox.js') }}"></script>
    @endif
@endpush
