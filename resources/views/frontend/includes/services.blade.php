    <div class="tp-blog-2__area tp-blog-2__space programs-home-grid">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                    <div class="tp-blog-2__section-title pb-50 text-center">
                        {{-- <span class="about-home-eyebrow d-block mb-2">What we offer</span> --}}
                        <h4 class="tp-section-title">Our Programs</h4>
                        <p class="text-muted mt-3 mb-0 mx-auto" style="max-width: 36rem;">Vocational training, entrepreneurship, discipleship, and community care for mothers and families across Uganda.</p>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                @foreach ($ourPrograms as $rs)
                <div class="col-lg-6 mb-0 wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s"
                data-wow-delay=".3s">
                    <article class="program-list-card h-100 d-flex flex-column bg-white rounded-3 overflow-hidden border shadow-sm">
                        <a href="{{ route('project', ['slug' => $rs->slug]) }}" class="program-list-card__thumb d-block position-relative">
                            <img src="{{ asset('storage/' . $rs->image) }}" alt="{{ $rs->title }}" class="w-100 program-list-card__img">
                        </a>
                        <div class="program-list-card__body p-4 d-flex flex-column flex-grow-1">
                            <h2 class="h5 mb-3">
                                <a href="{{ route('project', ['slug' => $rs->slug]) }}" class="text-dark text-decoration-none">{{ $rs->title }}</a>
                            </h2>
                            <div class="tp-about-3__text flex-grow-1">
                                @php
                                $descPlain = strip_tags(html_entity_decode($rs->description ?? ''));
                                $words = Str::limit($descPlain, 140, '…');
                                @endphp

                                <p class="program-list-card__excerpt mb-4">{{ $words }}</p>

                                <a href="{{ route('project', ['slug' => $rs->slug]) }}" class="tp-btn program-list-card__cta align-self-start mt-auto">
                                    View more <span class="ms-1" aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach

            </div>
        </div>
    </div>
