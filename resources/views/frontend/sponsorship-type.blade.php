@extends('layouts.frontbase')

@section('title', $meta['label'])

@section('content')
@include('frontend.includes.page-header', [
    'title' => $meta['label'],
    'caption' => $meta['caption'],
])

<section class="sponsorship-type-page pt-90 pb-90 grey-bg">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm sponsorship-type-page__intro">
                    <div class="card-body p-4 p-lg-5">
                        <span class="about-home-eyebrow d-block mb-2">Sponsorship</span>
                        <div class="postbox__text sponsorship-type-page__intro-text">{!! $meta['intro'] !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($profiles as $profile)
                <div class="col-md-6 col-lg-4">
                    @include('frontend.includes.sponsorship-card', ['profile' => $profile])
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Profiles for this category will appear here once published by our team.
                        You can still <a href="{{ route('getInvolved') }}">get involved</a> in other ways.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
