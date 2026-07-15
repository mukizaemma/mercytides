@extends('layouts.frontbase')

@section('title', $profile->displayName())

@php
    use App\Support\MercyTidesContent;

    $firstName = explode(' ', $profile->displayName())[0] ?? $profile->displayName();
    $supportOptions = MercyTidesContent::sponsorshipSupportFocusOptions();
    $embedUrl = $profile->youtubeEmbedUrl();
    $hasStoryBlocks = !empty($profile->testimany) || !empty($profile->challenges) || !empty($profile->vision);
@endphp

@section('content')
@include('frontend.includes.page-header', [
    'title' => $profile->displayName(),
    'caption' => $profile->typeLabel() . ($profile->age ? ' · Age '.$profile->age : ''),
    'pageKey' => 'sponsorship_profile',
])

<section class="sponsorship-profile-page pt-90 pb-90">
    <div class="container">
        {{-- Media row: portrait + landscape video --}}
        <div class="row g-4 align-items-stretch sponsorship-profile-page__media-row">
            <div class="{{ $embedUrl ? 'col-lg-5' : 'col-lg-6 mx-lg-auto' }}">
                <div class="sponsorship-profile-page__portrait">
                    <img src="{{ \App\Models\Sponsorship::publicImageUrl($profile->image) }}" alt="{{ $profile->displayName() }}" class="w-100">
                </div>
                <div class="sponsorship-profile-page__meta mt-3">
                    @if($profile->shouldShowStatusPublicly() && !empty($profile->status))
                        <span class="badge {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }}">{{ $profile->status }}</span>
                    @endif
                    @if(!empty($profile->monthly_need))
                        <p class="text-muted small mt-2 mb-0">Suggested support: <strong>${{ $profile->monthly_need }}</strong> / month</p>
                    @endif
                </div>
            </div>

            @if($embedUrl)
                <div class="col-lg-7">
                    <div class="sponsorship-profile-page__video">
                        <h2 class="sponsorship-profile-page__video-label">Hear from {{ $firstName }}</h2>
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm sponsorship-profile-page__video-frame">
                            <iframe src="{{ $embedUrl }}" title="Video about {{ $profile->displayName() }}" allowfullscreen loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Ways to support --}}
        <div class="sponsorship-profile-page__support mt-5 pt-2">
            <div class="sponsorship-profile-page__support-header text-center mb-4">
                <h2 class="sponsorship-profile-page__heading mb-2">Ways to support {{ $firstName }}</h2>
                <p class="text-muted mb-0 mx-auto sponsorship-profile-page__support-lead">
                    Choose how you would like to walk with her. This is a commitment of interest — we will follow up with secure giving options.
                </p>
            </div>

            <div class="row g-3 sponsorship-profile-page__support-grid">
                @foreach($supportOptions as $option)
                    <div class="col-sm-6 col-lg-4">
                        <button type="button"
                                class="sponsorship-support-card js-open-sponsor-commitment"
                                data-bs-toggle="modal"
                                data-bs-target="#sponsorCommitmentModal"
                                data-support-focus="{{ $option['key'] }}">
                            <span class="sponsorship-support-card__icon" aria-hidden="true">
                                <i class="fas {{ $option['icon'] }}"></i>
                            </span>
                            <span class="sponsorship-support-card__title">{{ $option['label'] }}</span>
                            <span class="sponsorship-support-card__text">{{ $option['text'] }}</span>
                            <span class="sponsorship-support-card__cta">Express interest</span>
                        </button>
                    </div>
                @endforeach
            </div>

            <p class="text-muted small text-center mt-4 mb-0">
                Mercy Tides will contact you with secure giving options. Nothing is charged on this page.
            </p>
        </div>

        @if($hasStoryBlocks)
            <div class="sponsorship-profile-page__story mt-5">
                @if(!empty($profile->testimany))
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Their story</h2>
                        <div class="postbox__text">{!! $profile->testimany !!}</div>
                    </div>
                @endif

                @if(!empty($profile->challenges))
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Challenges they face</h2>
                        <div class="postbox__text">{!! $profile->challenges !!}</div>
                    </div>
                @endif

                @if(!empty($profile->vision))
                    <div class="sponsorship-profile-page__block">
                        <h2 class="sponsorship-profile-page__heading">Their vision</h2>
                        <div class="postbox__text">{!! $profile->vision !!}</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-5">
            <a href="{{ route($typeMeta['route']) }}" class="tp-btn tp-btn--outline">
                <i class="fas fa-arrow-left me-2" aria-hidden="true"></i> Back to {{ $typeMeta['label'] }}
            </a>
        </div>
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
