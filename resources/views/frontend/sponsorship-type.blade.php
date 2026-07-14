@extends('layouts.frontbase')

@section('title', $meta['label'])

@section('content')
@include('frontend.includes.page-header', [
    'title' => $meta['label'],
    'caption' => $meta['caption'],
    'pageKey' => match ($type ?? '') {
        'child' => 'sponsor_child',
        'family' => 'sponsor_family',
        default => 'sponsor_young_mother',
    },
])

@php
    $isYoungMother = ($type ?? '') === 'young_mother';
    $pillars = $meta['pillars'] ?? [];
    $gridTitle = $meta['grid_title'] ?? 'Choose someone to walk with';
    $gridLead = $meta['grid_lead'] ?? 'Each profile shares a real story — read theirs, pray, and begin a sponsorship journey.';
@endphp

<section class="sponsorship-type-page pt-90 pb-90 grey-bg{{ $isYoungMother ? ' sponsorship-type-page--mothers' : '' }}">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-lg-10">
                <div class="card border-0 sponsorship-type-page__intro">
                    <div class="card-body p-4 p-lg-5">
                        <span class="about-home-eyebrow d-block mb-2">{{ $isYoungMother ? 'Mothers & hope' : 'Sponsorship' }}</span>
                        <h2 class="about-home-title h3 mb-3">{{ $meta['intro_title'] ?? $meta['label'] }}</h2>
                        <div class="postbox__text sponsorship-type-page__intro-text">{!! $meta['intro'] !!}</div>

                        @if(!empty($pillars))
                            <div class="sponsorship-type-page__pillars" role="list">
                                @foreach($pillars as $pillar)
                                    <div class="sponsorship-type-page__pillar" role="listitem">
                                        @if(!empty($pillar['icon']))
                                            <span class="sponsorship-type-page__pillar-icon" aria-hidden="true">
                                                <i class="fas {{ $pillar['icon'] }}"></i>
                                            </span>
                                        @endif
                                        <h3>{{ $pillar['title'] }}</h3>
                                        <p>{{ $pillar['text'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="sponsorship-type-page__grid-header">
            <span class="about-home-eyebrow d-block mb-2">{{ $isYoungMother ? 'Meet the mothers' : 'Open profiles' }}</span>
            <h2>{{ $gridTitle }}</h2>
            <p>{{ $gridLead }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($profiles as $profile)
                <div class="col-6 col-md-4">
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
