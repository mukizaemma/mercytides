@php
    $headerTitle = $title ?? '';
    $headerCaption = $caption ?? ($setting->page_header_caption ?? null);
    $pageKey = $pageKey ?? null;

    $headerImageUrl = \App\Support\PageHeaderImage::resolve($pageKey, $image ?? null);
    $hasCustomCover = ! empty($image) || (! empty($pageKey) && ! empty($headerImageUrl));
@endphp

<section
    class="tp-breadcrumb__area tp-breadcrumb--fullscreen p-relative fix{{ $hasCustomCover ? ' tp-breadcrumb__area--cover' : '' }}{{ $headerImageUrl ? '' : ' tp-breadcrumb--no-image' }}"
    aria-labelledby="page-header-title"
>
    @if($headerImageUrl)
        <div class="tp-breadcrumb__parallax-bg" data-background="{{ $headerImageUrl }}" aria-hidden="true"></div>
    @endif

    @unless($hasCustomCover)
        <div class="tp-breadcrumb__shape-1 z-index-5">
            <img src="{{ asset('assets/img/breadcrumb/breadcrumb-shape-1.png') }}" alt="" aria-hidden="true">
        </div>
        <div class="tp-breadcrumb__shape-2 z-index-5">
            <img src="{{ asset('assets/img/breadcrumb/breadcrumb-shape-2.png') }}" alt="" aria-hidden="true">
        </div>
    @endunless

    <div class="container tp-breadcrumb__container">
        <div class="row">
            <div class="col-12">
                <div class="tp-breadcrumb__content z-index-5 text-center">
                    <div class="page-header__top">
                        <h1 id="page-header-title" class="tp-breadcrumb__title text-center mb-0">{{ $headerTitle }}</h1>
                    </div>
                    @if(!empty($headerCaption))
                        <p class="tp-breadcrumb__caption text-center mb-0 mt-3">{{ $headerCaption }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
