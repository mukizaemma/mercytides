@extends('layouts.frontbase')

@section('title', $profile->displayName())

@php
    use App\Support\MercyTidesContent;
    use Illuminate\Support\Str;

    $firstName = explode(' ', $profile->displayName())[0] ?? $profile->displayName();
    $supportOptions = MercyTidesContent::sponsorshipSupportFocusOptions();
    $uploadedVideoUrl = $profile->uploadedVideoUrl();
    $embedUrl = $uploadedVideoUrl ? null : $profile->youtubeEmbedUrl();
    $hasVideo = (bool) ($uploadedVideoUrl || $embedUrl);
    $posterUrl = \App\Models\Sponsorship::publicImageUrl($profile->image);

    $filledHtml = static function (?string $html): bool {
        return trim(strip_tags(html_entity_decode((string) $html))) !== '';
    };

    $storyHtml = $filledHtml($profile->testimany ?? null) ? $profile->testimany : null;
    $challengesHtml = $filledHtml($profile->challenges ?? null) ? $profile->challenges : null;
    $visionHtml = $filledHtml($profile->vision ?? null) ? $profile->vision : null;

    $storyExcerpt = $storyHtml
        ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string) $profile->testimany)))), 320, '…')
        : trim(strip_tags(html_entity_decode((string) ($typeMeta['caption'] ?? ''))));

    $relatedProfiles = $relatedProfiles ?? collect();
    $relatedTitle = match ($profile->type) {
        'young_mother' => 'More mothers to support',
        'child' => 'More children to support',
        'family' => 'More families to support',
        default => 'More profiles to support',
    };

    $sponsorCta = 'Sponsor ' . $firstName;
@endphp

@section('content')
<section class="sp-profile">
    <div class="container sp-profile__container">

        {{-- Hero: photo + story + CTA --}}
        <div class="sp-profile__hero">
            <div class="sp-profile__hero-media">
                <figure class="sp-profile__portrait">
                    <img
                        src="{{ $posterUrl }}"
                        alt="{{ $profile->displayName() }}"
                        width="720"
                        height="960"
                        loading="eager"
                        decoding="async"
                    >
                </figure>
            </div>

            <div class="sp-profile__hero-copy">
                <p class="sp-profile__eyebrow">{{ $profile->typeLabel() }}@if($profile->age) · Age {{ $profile->age }}@endif</p>
                <h1 class="sp-profile__name">{{ $profile->displayName() }}</h1>

                @if($profile->shouldShowStatusPublicly() && !empty($profile->status) || !empty($profile->monthly_need))
                    <div class="sp-profile__meta">
                        @if($profile->shouldShowStatusPublicly() && !empty($profile->status))
                            <span class="sp-profile__chip {{ $profile->isAvailable() ? 'sp-profile__chip--open' : 'sp-profile__chip--sponsored' }}">
                                {{ $profile->status }}
                            </span>
                        @endif
                        @if(!empty($profile->monthly_need))
                            <span class="sp-profile__need">Suggested <strong>${{ $profile->monthly_need }}</strong>/month</span>
                        @endif
                    </div>
                @endif

                @if($storyExcerpt !== '')
                    <p class="sp-profile__lede">{{ $storyExcerpt }}</p>
                @endif

                <div class="sp-profile__cta-group">
                    <button
                        type="button"
                        class="sp-profile__btn-primary js-open-sponsor-commitment"
                        data-bs-toggle="modal"
                        data-bs-target="#sponsorCommitmentModal"
                        data-support-focus="full_care"
                    >
                        {{ $sponsorCta }}
                    </button>

                    <div class="sp-profile__cta-secondary">
                        <a href="#ways-to-support" class="sp-profile__link">See ways to help</a>
                        @if($hasVideo)
                            <span class="sp-profile__dot" aria-hidden="true">·</span>
                            <a href="#her-video" class="sp-profile__link">Watch her story</a>
                        @endif
                    </div>
                </div>

                <p class="sp-profile__trust">No payment is taken on this page. We’ll follow up with secure next steps.</p>
            </div>
        </div>

        {{-- Optional video --}}
        @if($hasVideo)
            <section id="her-video" class="sp-profile__video-section" aria-labelledby="her-video-heading">
                <div class="sp-profile__section-head sp-profile__section-head--left">
                    <h2 id="her-video-heading" class="sp-profile__section-title">Hear from {{ $firstName }}</h2>
                    <p class="sp-profile__section-lead">Watch her share her journey in her own words.</p>
                </div>
                <div class="sp-profile__video-card">
                    <div class="ratio ratio-16x9 sp-profile__video-frame">
                        @if($uploadedVideoUrl)
                            @php
                                $videoExt = strtolower(pathinfo((string) $profile->video_path, PATHINFO_EXTENSION));
                                $videoMime = match ($videoExt) {
                                    'webm' => 'video/webm',
                                    'mov' => 'video/quicktime',
                                    default => 'video/mp4',
                                };
                            @endphp
                            <video
                                class="sp-profile__native-video"
                                controls
                                playsinline
                                preload="metadata"
                                @if($posterUrl) poster="{{ $posterUrl }}" @endif
                            >
                                <source src="{{ $uploadedVideoUrl }}" type="{{ $videoMime }}">
                                Your browser does not support embedded video.
                            </video>
                        @else
                            <iframe
                                src="{{ $embedUrl }}"
                                title="Video about {{ $profile->displayName() }}"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- Longer story blocks only when filled --}}
        @if($storyHtml || $challengesHtml || $visionHtml)
            <section class="sp-profile__story" aria-label="Her full story">
                @if($storyHtml)
                    <article class="sp-profile__story-block">
                        <h2 class="sp-profile__section-title">Her story</h2>
                        <div class="sp-profile__prose postbox__text">{!! $storyHtml !!}</div>
                    </article>
                @endif
                @if($challengesHtml)
                    <article class="sp-profile__story-block">
                        <h2 class="sp-profile__section-title">Challenges she faces</h2>
                        <div class="sp-profile__prose postbox__text">{!! $challengesHtml !!}</div>
                    </article>
                @endif
                @if($visionHtml)
                    <article class="sp-profile__story-block">
                        <h2 class="sp-profile__section-title">Her vision</h2>
                        <div class="sp-profile__prose postbox__text">{!! $visionHtml !!}</div>
                    </article>
                @endif
            </section>
        @endif

        {{-- Ways to support cards --}}
        <section id="ways-to-support" class="sp-profile__support" aria-labelledby="ways-to-support-heading">
            <div class="sp-profile__section-head">
                <h2 id="ways-to-support-heading" class="sp-profile__section-title">Ways to support {{ $firstName }}</h2>
                <p class="sp-profile__section-lead">
                    Choose a focus. We will follow up with secure giving options — nothing is charged on this page.
                </p>
            </div>

            <div class="sp-support-grid" role="list">
                @foreach($supportOptions as $option)
                    <button
                        type="button"
                        class="sp-support-card js-open-sponsor-commitment{{ $option['key'] === 'full_care' ? ' sp-support-card--featured' : '' }}"
                        role="listitem"
                        data-bs-toggle="modal"
                        data-bs-target="#sponsorCommitmentModal"
                        data-support-focus="{{ $option['key'] }}"
                    >
                        @if($option['key'] === 'full_care')
                            <span class="sp-support-card__badge">Most complete</span>
                        @endif
                        <span class="sp-support-card__icon" aria-hidden="true">
                            <i class="fas {{ $option['icon'] }}"></i>
                        </span>
                        <span class="sp-support-card__title">{{ $option['label'] }}</span>
                        <span class="sp-support-card__text">{{ $option['text'] }}</span>
                        <span class="sp-support-card__action">
                            Choose this
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </span>
                    </button>
                @endforeach
            </div>

            <div class="sp-profile__support-footer">
                <button
                    type="button"
                    class="sp-profile__btn-primary js-open-sponsor-commitment"
                    data-bs-toggle="modal"
                    data-bs-target="#sponsorCommitmentModal"
                    data-support-focus="full_care"
                >
                    {{ $sponsorCta }}
                </button>
            </div>
        </section>

        {{-- Related profiles --}}
        @if($relatedProfiles->isNotEmpty())
            <section class="sp-profile__related" aria-labelledby="related-heading">
                <div class="sp-profile__related-head">
                    <div>
                        <h2 id="related-heading" class="sp-profile__section-title">{{ $relatedTitle }}</h2>
                        <p class="sp-profile__section-lead mb-0">Meet others seeking skills, care, and a path toward dignity.</p>
                    </div>
                    <a href="{{ route($typeMeta['route']) }}" class="sp-profile__link sp-profile__link--standout">View all</a>
                </div>

                <div class="sp-related-grid">
                    @foreach($relatedProfiles->take(4) as $related)
                        @php
                            $relatedName = $related->displayName();
                            $relatedFirst = explode(' ', $relatedName)[0] ?? $relatedName;
                            $relatedImg = \App\Models\Sponsorship::publicImageUrl($related->image);
                        @endphp
                        <a href="{{ $related->profileRoute() }}" class="sp-related-card">
                            <span class="sp-related-card__media">
                                <img src="{{ $relatedImg }}" alt="{{ $relatedName }}" loading="lazy" width="400" height="500">
                            </span>
                            <span class="sp-related-card__body">
                                <span class="sp-related-card__name">{{ $relatedName }}</span>
                                @if(!empty($related->age))
                                    <span class="sp-related-card__meta">Age {{ $related->age }}</span>
                                @endif
                                <span class="sp-related-card__cta">Meet {{ $relatedFirst }} <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>

{{-- Mobile sticky primary CTA --}}
<div class="sp-profile__sticky d-md-none">
    <button
        type="button"
        class="sp-profile__btn-primary sp-profile__btn-primary--block js-open-sponsor-commitment"
        data-bs-toggle="modal"
        data-bs-target="#sponsorCommitmentModal"
        data-support-focus="full_care"
    >
        {{ $sponsorCta }}
    </button>
</div>

<div class="modal fade" id="sponsorCommitmentModal" tabindex="-1" aria-labelledby="sponsorCommitmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content sponsorship-commitment-modal">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title h5 visually-hidden" id="sponsorCommitmentModalLabel">{{ $sponsorCta }}</h2>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2 px-4 px-lg-5 pb-4">
                @include('frontend.includes.sponsor-inquiry-form', ['profile' => $profile])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/sponsor-inquiry-form.js') }}" defer></script>
@endpush
