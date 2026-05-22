@if(($latestNews ?? collect())->isNotEmpty())
<section class="tp-blog-2__area tp-blog-2__space">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="tp-blog-2__section-title pb-50 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                    <span class="about-home-eyebrow d-block mb-2">Stay informed</span>
                    <h4 class="tp-section-title">Latest news &amp; events</h4>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @foreach ($latestNews as $post)
                <div class="col-md-4 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".2s">
                    <article class="program-list-card h-100 d-flex flex-column bg-white rounded-3 overflow-hidden border shadow-sm">
                        @if(!empty($post->image))
                            <a href="{{ route('postSingle', ['slug' => $post->slug]) }}" class="program-list-card__thumb d-block">
                                <img src="{{ asset('storage/' . ltrim($post->image, '/')) }}" alt="{{ $post->title }}" class="w-100 program-list-card__img" style="height:220px;">
                            </a>
                        @endif
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h3 class="h5 mb-2">
                                <a href="{{ route('postSingle', ['slug' => $post->slug]) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                            </h3>
                            <p class="program-list-card__excerpt flex-grow-1">{{ \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($post->body ?? $post->description ?? '')), 120, '…') }}</p>
                            <a href="{{ route('postSingle', ['slug' => $post->slug]) }}" class="tp-btn program-list-card__cta align-self-start mt-2">Read more</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="row pt-4">
            <div class="col-12 text-center">
                <a href="{{ route('posts') }}" class="tp-btn">View all updates</a>
            </div>
        </div>
    </div>
</section>
@endif
