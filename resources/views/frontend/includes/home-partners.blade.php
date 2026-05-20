@if(($partners ?? collect())->isNotEmpty())
<section class="py-70 grey-bg home-partners-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center pb-40">
                <h4 class="tp-section-title">Partners &amp; churches</h4>
                <p class="text-muted mb-0">Walking together to serve mothers and families across Uganda.</p>
            </div>
        </div>
        <div class="row g-4 align-items-center justify-content-center">
            @foreach ($partners as $partner)
                <div class="col-6 col-md-4 col-lg-3 text-center">
                    @if(!empty($partner->image))
                        <img src="{{ asset('storage/images/partners/' . $partner->image) }}" alt="{{ $partner->names ?? 'Partner' }}" class="home-partners-section__logo img-fluid" loading="lazy">
                    @else
                        <span class="home-partners-section__name d-inline-block px-3 py-2">{{ $partner->names ?? 'Partner' }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
