@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Gallery',
    'pageKey' => 'gallery',
])

<section class="site-gallery pt-90 pb-100">
    <div class="container">
        <div class="site-gallery__intro text-center mb-50">
            <p class="mb-0">Moments from our programs, mothers, and community — photos and films that tell the story of hope in motion.</p>
        </div>

        @if($gallery->count())
            <div class="row g-4" id="siteGalleryGrid">
                @foreach($gallery as $index => $item)
                    <div class="col-lg-4 col-md-6">
                        <button
                            type="button"
                            class="site-gallery__tile"
                            data-gallery-open
                            data-index="{{ $index }}"
                            data-type="{{ $item->isVideo() ? 'video' : 'image' }}"
                            data-src="{{ $item->lightboxSrc() }}"
                            data-caption="{{ e($item->caption ?? '') }}"
                            aria-label="{{ $item->isVideo() ? 'Play video' : 'View image' }}{{ $item->caption ? ': '.$item->caption : '' }}"
                        >
                            <span class="site-gallery__media">
                                <img src="{{ $item->thumbUrl() }}" alt="{{ $item->caption ?: 'Gallery item' }}" loading="lazy">
                                @if($item->isVideo())
                                    <span class="site-gallery__play" aria-hidden="true">
                                        <i class="fas fa-play"></i>
                                    </span>
                                @endif
                                <span class="site-gallery__zoom" aria-hidden="true">
                                    <i class="{{ $item->isVideo() ? 'fas fa-play' : 'far fa-expand' }}"></i>
                                </span>
                            </span>
                            @if(!empty($item->caption))
                                <span class="site-gallery__caption">{{ $item->caption }}</span>
                            @endif
                        </button>
                    </div>
                @endforeach
            </div>

            @if($gallery->hasPages())
                <div class="site-gallery__pager mt-50 d-flex justify-content-center">
                    {{ $gallery->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <p class="mb-0 text-muted">Gallery items will appear here soon.</p>
            </div>
        @endif
    </div>
</section>

{{-- Lightbox: image or YouTube, with prev/next and close --}}
<div class="gallery-lightbox" id="galleryLightbox" hidden aria-hidden="true">
    <div class="gallery-lightbox__backdrop" data-gallery-close></div>
    <div class="gallery-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Gallery viewer">
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
                    title="YouTube video"
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

@endsection

@push('scripts')
<script src="{{ asset('assets/js/site-gallery-lightbox.js') }}" defer></script>
@endpush
