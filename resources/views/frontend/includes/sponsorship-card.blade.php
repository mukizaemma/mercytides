<article class="sponsor-card bg-white h-100 d-flex flex-column">
    @if(!empty($profile->image))
        <a href="{{ $profile->profileRoute() }}" class="sponsor-card__media d-block" tabindex="-1" aria-hidden="true">
            <img src="{{ \App\Models\Sponsorship::publicImageUrl($profile->image) }}" alt="{{ $profile->displayName() }}" class="w-100 sponsor-card__img" loading="lazy">
        </a>
    @else
        <div class="sponsor-card__img d-flex align-items-center justify-content-center bg-light text-muted">Photo coming soon</div>
    @endif
    <div class="p-4 d-flex flex-column flex-grow-1">
        <h3 class="h5 mb-1">
            <a href="{{ $profile->profileRoute() }}" class="text-decoration-none text-dark">{{ $profile->displayName() }}</a>
        </h3>
        <p class="text-muted small mb-2">
            @if(!empty($profile->age))Age {{ $profile->age }}@endif
            @if(!empty($profile->monthly_need)) · ${{ $profile->monthly_need }}/mo suggested @endif
        </p>
        <p class="flex-grow-1 sponsor-card__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($profile->testimany ?? ''), 140, '…') }}</p>
        <span class="badge {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }} mb-3 align-self-start">
            {{ $profile->status ?? 'Available' }}
        </span>
        <a href="{{ $profile->profileRoute() }}" class="tp-btn align-self-start">Meet {{ explode(' ', $profile->displayName())[0] ?? 'them' }}</a>
    </div>
</article>
