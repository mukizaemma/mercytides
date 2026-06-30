@extends('layouts.frontbase')

@section('title', $mother->displayName())

@section('content')
    @include('frontend.includes.page-header', [
        'title' => $mother->displayName(),
    ])

    <section class="mother-profile-page pt-90 pb-90">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-5 col-xl-4">
                    <div class="mother-profile-page__portrait">
                        <img src="{{ \App\Models\Mother::publicImageUrl($mother->image) }}" alt="{{ $mother->displayName() }}" class="w-100">
                    </div>
                    @if(!empty($mother->age))
                        <p class="mother-profile-page__meta text-center mt-3 mb-0">Age {{ $mother->age }}</p>
                    @endif
                </div>

                <div class="col-lg-7 col-xl-8">
                    <div class="mother-profile-page__content">
                        @if(!empty($mother->description))
                            <div class="mother-profile-page__block">
                                <h2 class="mother-profile-page__heading">Her story</h2>
                                <div class="postbox__text mother-profile-page__text">{!! $mother->description !!}</div>
                            </div>
                        @endif

                        @if(!empty($mother->vision))
                            <div class="mother-profile-page__block">
                                <h2 class="mother-profile-page__heading">Her vision</h2>
                                <div class="postbox__text mother-profile-page__text">{!! $mother->vision !!}</div>
                            </div>
                        @endif

                        @if(empty($mother->description) && empty($mother->vision))
                            <p class="text-muted mb-4">More of her story will be shared here soon.</p>
                        @endif

                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('getInvolved') }}" class="tp-btn">Get involved</a>
                            <a href="{{ route('sponsorship.youngMother') }}" class="tp-btn tp-btn--outline">Back to gallery</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
