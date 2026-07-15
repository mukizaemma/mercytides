@extends('layouts.frontbase')

@section('title', $profile->displayName())

@php
    use App\Support\MercyTidesContent;

    $firstName = explode(' ', $profile->displayName())[0] ?? $profile->displayName();
    $supportOptions = MercyTidesContent::sponsorshipSupportFocusOptions();
    $featuredSupport = collect($supportOptions)->firstWhere('key', 'full_care');
    $otherSupport = collect($supportOptions)->reject(fn ($option) => $option['key'] === 'full_care')->values();
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
    $relatedProfiles = $relatedProfiles ?? collect();

    $relatedTitle = match ($profile->type) {
        'young_mother' => 'More mothers',
        'child' => 'More children',
        'family' => 'More families',
        default => 'More profiles',
    };
@endphp

@section('content')
<section class="sponsorship-profile-page">
    <div class="container sponsorship-profile-page__inner">
        <div class="row g-4 g-xl-5 sponsorship-profile-page__hero-row">
            {{-- Meet her --}}
            <div class="{{ $hasVideo ? 'col-lg-5' : 'col-lg-6 mx-lg-auto' }}">
                <div class="sponsorship-profile-page__portrait">
                    <img src="{{ $posterUrl }}" alt="{{ $profile->displayName() }}" width="640" height="853" loading="eager">
                </div>

                <div class="sponsorship-profile-page__identity">
                    <p class="sponsorship-profile-page__eyebrow">{{ $profile->typeLabel() }}@if($profile->age) · Age {{ $profile->age }}@endif</p>
                    <h1 class="sponsorship-profile-page__name">{{ $profile->displayName() }}</h1>

                    <div class="sponsorship-profile-page__meta-row">
                        @if($profile->shouldShowStatusPublicly() && !empty($profile->status))
                            <span class="badge sponsorship-profile-page__status {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }}">{{ $profile->status }}</span>
                        @endif
                        @if(!empty($profile->monthly_need))
                            <span class="sponsorship-profile-page__need">From <strong>${{ $profile->monthly_need }}</strong> / month</span>
                        @endif
                    </div>

                    <div class="sponsorship-profile-page__actions">
                        <button type="button"
                                class="tp-btn js-open-sponsor-commitment"
                                data-bs-toggle="modal"
                                data-bs-target="#sponsorCommitmentModal"
                                data-support-focus="full_care">
                            Support {{ $firstName }}
                        </button>
                        <a href="#ways-to-support" class="sponsorship-profile-page__text-link">See ways to help</a>
                    </div>
                </div>
            </div>

            {{-- Hear her --}}
            @if($hasVideo)
                <div class="col-lg-7">
                    <div class="sponsorship-profile-page__video-panel">
                        <div class="sponsorship-profile-page__video-head">
                            <p class="sponsorship-profile-page__video-label">Hear from {{ $firstName }}</p>
                            <p class="sponsorship-profile-page__video-hint mb-0">Watch her share her journey</p>
                        </div>
                        <div class="ratio ratio-16x9 sponsorship-profile-page__video-frame">
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
                                    class="sponsorship-profile-page__native-video"
                                    controls
                                    playsinline
                                    preload="metadata"
                                    @if($posterUrl) poster="{{ $posterUrl }}" @endif
                                >
                                    <source src="{{ $uploadedVideoUrl }}" type="{{ $videoMime }}">
                                    Your browser does not support embedded video.
                                </video>
                            @else
                                <iframe src="{{ $embedUrl }}" title="Video about {{ $profile->displayName() }}" allowfullscreen loading="lazy"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($storyHtml || $challengesHtml || $visionHtml)
            <div class="sponsorship-profile-page__story">
                @if($storyHtml)
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Her story</h2>
                        <div class="postbox__text">{!! $storyHtml !!}</div>
                    </div>
                @endif
                @if($challengesHtml)
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Challenges she faces</h2>
                        <div class="postbox__text">{!! $challengesHtml !!}</div>
                    </div>
                @endif
                @if($visionHtml)
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Her vision</h2>
                        <div class="postbox__text">{!! $visionHtml !!}</div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Decide how to help --}}
        <section id="ways-to-support" class="sponsorship-profile-page__support" aria-labelledby="ways-to-support-heading">
            <div class="sponsorship-profile-page__support-header">
                <h2 id="ways-to-support-heading" class="sponsorship-profile-page__heading">How would you like to help {{ $firstName }}?</h2>
                <p class="sponsorship-profile-page__support-lead mb-0">
                    Pick one focus to start. We’ll follow up with next steps — nothing is charged here.
                </p>
            </div>

            @if($featuredSupport)
                <button type="button"
                        class="sponsorship-support-featured js-open-sponsor-commitment"
                        data-bs-toggle="modal"
                        data-bs-target="#sponsorCommitmentModal"
                        data-support-focus="{{ $featuredSupport['key'] }}">
                    <span class="sponsorship-support-featured__badge">Recommended</span>
                    <span class="sponsorship-support-featured__body">
                        <span class="sponsorship-support-featured__icon" aria-hidden="true"><i class="fas {{ $featuredSupport['icon'] }}"></i></span>
                        <span class="sponsorship-support-featured__copy">
                            <span class="sponsorship-support-featured__title">{{ $featuredSupport['label'] }}</span>
                            <span class="sponsorship-support-featured__text">{{ $featuredSupport['text'] }}</span>
                        </span>
                    </span>
                    <span class="sponsorship-support-featured__cta">Choose this</span>
                </button>
            @endif

            <div class="sponsorship-support-grid" role="list">
                @foreach($otherSupport as $option)
                    <button type="button"
                            class="sponsorship-support-card js-open-sponsor-commitment"
                            role="listitem"
                            data-bs-toggle="modal"
                            data-bs-target="#sponsorCommitmentModal"
                            data-support-focus="{{ $option['key'] }}">
                        <span class="sponsorship-support-card__icon" aria-hidden="true">
                            <i class="fas {{ $option['icon'] }}"></i>
                        </span>
                        <span class="sponsorship-support-card__copy">
                            <span class="sponsorship-support-card__title">{{ $option['label'] }}</span>
                            <span class="sponsorship-support-card__text">{{ $option['text'] }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </section>

        {{-- Related --}}
        @if($relatedProfiles->isNotEmpty())
            <section class="sponsorship-profile-page__related" aria-labelledby="related-profiles-heading">
                <div class="sponsorship-profile-page__related-head">
                    <h2 id="related-profiles-heading" class="sponsorship-profile-page__heading mb-1">{{ $relatedTitle }}</h2>
                    <a href="{{ route($typeMeta['route']) }}" class="sponsorship-profile-page__text-link">View all</a>
                </div>

                <div class="sponsorship-related-grid">
                    @foreach($relatedProfiles->take(4) as $related)
                        @php
                            $relatedName = $related->displayName();
                            $relatedFirst = explode(' ', $relatedName)[0] ?? $relatedName;
                            $relatedImg = \App\Models\Sponsorship::publicImageUrl($related->image);
                        @endphp
                        <a href="{{ $related->profileRoute() }}" class="sponsorship-related-card">
                            <span class="sponsorship-related-card__media">
                                <img src="{{ $relatedImg }}" alt="{{ $relatedName }}" loading="lazy" width="320" height="400">
                            </span>
                            <span class="sponsorship-related-card__name">{{ $relatedName }}</span>
                            <span class="sponsorship-related-card__cta">Meet {{ $relatedFirst }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>

<div class="sponsorship-profile-page__sticky-cta d-md-none">
    <button type="button"
            class="tp-btn w-100 js-open-sponsor-commitment"
            data-bs-toggle="modal"
            data-bs-target="#sponsorCommitmentModal"
            data-support-focus="full_care">
        Support {{ $firstName }}
    </button>
</div>

<div class="modal fade" id="sponsorCommitmentModal" tabindex="-1" aria-labelledby="sponsorCommitmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content sponsorship-commitment-modal">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title h5 visually-hidden" id="sponsorCommitmentModalLabel">Commit to support {{ $profile->displayName() }}</h2>
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
