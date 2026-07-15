@extends('layouts.frontbase')

@section('title', $profile->displayName())

@php
    use App\Support\MercyTidesContent;

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
    $relatedProfiles = $relatedProfiles ?? collect();
@endphp

@section('content')
<section class="sponsorship-profile-page">
    <div class="container sponsorship-profile-page__inner">
        {{-- Identity + media --}}
        <header class="sponsorship-profile-page__intro text-center">
            <p class="sponsorship-profile-page__eyebrow mb-2">{{ $profile->typeLabel() }}@if($profile->age) · Age {{ $profile->age }}@endif</p>
            <h1 class="sponsorship-profile-page__name">{{ $profile->displayName() }}</h1>
            @if($profile->shouldShowStatusPublicly() && !empty($profile->status))
                <span class="badge sponsorship-profile-page__status {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }}">{{ $profile->status }}</span>
            @endif
            @if(!empty($profile->monthly_need))
                <p class="sponsorship-profile-page__need mb-0">Suggested support: <strong>${{ $profile->monthly_need }}</strong> / month</p>
            @endif
        </header>

        <div class="row g-4 g-xl-5 align-items-stretch sponsorship-profile-page__media-row">
            <div class="{{ $hasVideo ? 'col-lg-5' : 'col-lg-5 mx-lg-auto' }}">
                <div class="sponsorship-profile-page__portrait">
                    <img src="{{ $posterUrl }}" alt="{{ $profile->displayName() }}" class="w-100" width="640" height="853">
                </div>
            </div>

            @if($hasVideo)
                <div class="col-lg-7">
                    <div class="sponsorship-profile-page__video">
                        <p class="sponsorship-profile-page__video-label mb-3">Hear from {{ $firstName }}</p>
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

        {{-- CTAs under details --}}
        <div class="sponsorship-profile-page__actions d-flex flex-wrap justify-content-center gap-2 gap-md-3">
            <button type="button"
                    class="tp-btn js-open-sponsor-commitment"
                    data-bs-toggle="modal"
                    data-bs-target="#sponsorCommitmentModal"
                    data-support-focus="full_care">
                Support {{ $firstName }}
            </button>
            <a href="{{ route('getInvolved') }}" class="tp-btn tp-btn--outline">Get involved</a>
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

        {{-- Ways to support --}}
        <div class="sponsorship-profile-page__support">
            <div class="sponsorship-profile-page__support-header text-center">
                <p class="sponsorship-profile-page__eyebrow mb-2">Walk with {{ $firstName }}</p>
                <h2 class="sponsorship-profile-page__heading">Ways to support</h2>
                <p class="sponsorship-profile-page__support-lead mb-0">
                    Choose a focus. We will follow up with secure giving options — nothing is charged on this page.
                </p>
            </div>

            <div class="sponsorship-support-grid" role="list">
                @foreach($supportOptions as $option)
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
                        <span class="sponsorship-support-card__arrow" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- More mothers / related profiles --}}
        @if($relatedProfiles->isNotEmpty())
            @php
                $relatedTitle = match ($profile->type) {
                    'young_mother' => 'More mothers to support',
                    'child' => 'More children to support',
                    'family' => 'More families to support',
                    default => 'More profiles to support',
                };
            @endphp
            @include('frontend.includes.mothers-gallery', [
                'mothers' => $relatedProfiles,
                'limit' => 4,
                'sectionEyebrow' => 'Keep walking with others',
                'sectionTitle' => $relatedTitle,
                'sectionLead' => 'Meet others seeking skills, care, and a path toward dignity.',
                'viewMoreRoute' => route($typeMeta['route']),
                'viewMoreLabel' => 'View all',
            ])
        @endif
    </div>
</section>

{{-- Sticky mobile CTA --}}
<div class="sponsorship-profile-page__sticky-cta d-md-none">
    <button type="button"
            class="tp-btn w-100 js-open-sponsor-commitment"
            data-bs-toggle="modal"
            data-bs-target="#sponsorCommitmentModal">
        Support {{ $firstName }}
    </button>
</div>

{{-- Commitment modal --}}
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
