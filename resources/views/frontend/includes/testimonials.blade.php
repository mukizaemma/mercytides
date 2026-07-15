    <div class="tp-blog-2__area tp-blog-2__spaces">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                    <div class="tp-blog-2__section-title pb-50 text-center">
                        <h4 class="tp-section-title">Testimonials</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($testimonials as $rs)
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s"
                data-wow-delay=".3s">
                    <article class="testimonial-card">
                        <a href="{{ route('testimony',['id'=>$rs->id]) }}" class="testimonial-card__thumb d-block">
                            <img src="{{ asset('storage/' . $rs->image) }}" alt="{{ $rs->names }}">
                        </a>
                        <div class="testimonial-card__body">
                            <span class="testimonial-card__name">{{ $rs->names }}</span>
                            @php
                            $testPlain = strip_tags(html_entity_decode($rs->testimony ?? ''));
                            $words = Str::limit($testPlain, 120, '…');
                            @endphp
                            <p class="testimonial-card__quote">{{ $words }}</p>
                            @if(strlen($testPlain) > 120)
                                <a href="{{ route('testimony',['id'=>$rs->id]) }}" class="testimonial-card__link">
                                    Read full story <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>
                    </article>
                </div>
                @endforeach

            </div>

            <div class="row">
                <div class="tp-about-3__btn text-center">
                    <a class="tp-btn" href="{{route('testimonials')}}">View More Testimonials</a>
                </div>
            </div>
        </div>
    </div>