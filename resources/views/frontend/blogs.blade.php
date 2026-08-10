@extends('layouts.frontbase')

@section('title', 'Updates')

@section('content')

        @include('frontend.includes.page-header', [
            'title' => 'Recent Updates',
            'pageKey' => 'blog',
        ])

    <div class="tp-blog-2__area pt-120 pb-90">
        <div class="container">
            <div class="row">
                @forelse ($news as $blog)
                @php
                    $cover = $blog->image ? ltrim((string) $blog->image, '/') : '';
                    if ($cover !== '' && ! str_contains($cover, '/')) {
                        $cover = 'images/news/'.$cover;
                    }
                    $coverUrl = $cover !== '' ? asset('storage/'.$cover) : null;
                @endphp
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30 wow tpfadeUp" data-wow-duration=".9s"
                data-wow-delay=".3s">
                    <div class="tp-blog-2__item">
                        <a href="{{ route('postSingle', $blog->slug) }}">
                            <div class="tp-blog-2__thumb p-relative">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $blog->title }}">
                                @endif
                            </div>
                        </a>
                        <div class="tp-blog-2__content">
                            <a href="{{ route('postSingle', $blog->slug) }}"><h4 class="tp-blog-2__title-sm">{{ $blog->title }}</h4></a>
                            <span class="tp-blog-2__meta-3">{{ optional($blog->created_at)->format('d M, Y') }}</span>
                            <a href="{{ route('postSingle', $blog->slug) }}">
                                <div class="tp-blog-2__link text-center">
                                    <span>Read More<i class="flaticon-arrow-right"></i></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted mb-0">Updates from our community will appear here soon.</p>
                </div>
                @endforelse
            </div>
            @if(method_exists($news, 'hasPages') && $news->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $news->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @include('frontend.includes.backImage')

@endsection
