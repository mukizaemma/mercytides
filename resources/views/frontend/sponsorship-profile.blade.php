@extends('layouts.frontbase')

@section('title', $profile->displayName())

@section('content')
@include('frontend.includes.page-header', [
    'title' => $profile->displayName(),
    'caption' => $profile->typeLabel() . ($profile->age ? ' · Age '.$profile->age : ''),
    'pageKey' => 'sponsorship_profile',
])

<section class="sponsorship-profile-page pt-90 pb-90">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5 col-xl-4">
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

                @if($embedUrl = $profile->youtubeEmbedUrl())
                    <div class="sponsorship-profile-page__video mt-4">
                        <h2 class="h6 mb-2">Video</h2>
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                            <iframe src="{{ $embedUrl }}" title="Video about {{ $profile->displayName() }}" allowfullscreen loading="lazy"></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-7 col-xl-8">
                <div class="sponsorship-profile-page__content">
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

                    <div class="card border-0 shadow-sm site-form-card sponsorship-profile-page__form-card mt-4">
                        <div class="card-body p-4 p-lg-5">
                            @include('frontend.includes.sponsor-inquiry-form', ['profile' => $profile])
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route($typeMeta['route']) }}" class="tp-btn tp-btn--outline">
                            <i class="fas fa-arrow-left me-2" aria-hidden="true"></i> Back to {{ $typeMeta['label'] }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/sponsor-inquiry-form.js') }}" defer></script>
@endpush
