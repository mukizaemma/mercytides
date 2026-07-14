@php
    $displayName = $profile->displayName();
    $firstName = explode(' ', $displayName)[0] ?? '';
    $isGenericLabel = $firstName === '' || preg_match('/^(young|mother|child|family|#|\d)/i', $firstName);
    $isMother = ($profile->type ?? '') === 'young_mother';
    $ctaLabel = $isMother
        ? ($isGenericLabel ? 'View her story' : 'Walk with '.$firstName)
        : ($isGenericLabel ? 'View profile' : 'Meet '.$firstName);
    $excerpt = trim(strip_tags($profile->testimany ?? ''));
    if ($excerpt === '' && $isMother) {
        $excerpt = 'A young mother rebuilding her future through skills training, mentorship, and Christ-centered care for herself and her child.';
    }
@endphp
<article class="sponsor-card h-100 d-flex flex-column">
    @if(!empty($profile->image))
        <a href="{{ $profile->profileRoute() }}" class="sponsor-card__media" tabindex="-1" aria-hidden="true">
            <img src="{{ \App\Models\Sponsorship::publicImageUrl($profile->image) }}" alt="{{ $displayName }}" class="sponsor-card__img" loading="lazy">
        </a>
    @else
        <div class="sponsor-card__img sponsor-card__img--placeholder d-flex align-items-center justify-content-center bg-light text-muted">Photo coming soon</div>
    @endif
    <div class="sponsor-card__body d-flex flex-column flex-grow-1">
        <h3 class="sponsor-card__name">
            <a href="{{ $profile->profileRoute() }}" class="text-decoration-none">{{ $displayName }}</a>
        </h3>
        <p class="sponsor-card__meta">
            @if(!empty($profile->age))Age {{ $profile->age }}@endif
            @if(!empty($profile->monthly_need)){{ !empty($profile->age) ? ' · ' : '' }}${{ $profile->monthly_need }}/mo suggested @endif
        </p>
        @if($excerpt !== '')
            <p class="flex-grow-1 sponsor-card__excerpt">{{ \Illuminate\Support\Str::limit($excerpt, 120, '…') }}</p>
        @else
            <div class="flex-grow-1"></div>
        @endif
        <span class="badge sponsor-card__badge {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }} mb-3 align-self-start">
            {{ $profile->status ?? 'Available' }}
        </span>
        <a href="{{ $profile->profileRoute() }}" class="tp-btn align-self-start">{{ $ctaLabel }}</a>
    </div>
</article>
