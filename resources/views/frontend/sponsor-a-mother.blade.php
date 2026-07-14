@extends('layouts.frontbase')

@section('title', 'Sponsor a Mother')

@section('content')
@include('frontend.includes.page-header', [
    'title' => 'Sponsor a Mother',
    'caption' => 'Walk alongside a mother with monthly support, encouragement, and progress updates.',
    'pageKey' => 'sponsor_young_mother',
    'image' => $headerImage ?? null,
])

<section class="py-5 grey-bg">
    <div class="container">
        <div class="row g-4">
            @forelse($mothers as $mother)
                <div class="col-md-6 col-lg-4">
                    <article class="sponsor-card bg-white h-100 d-flex flex-column">
                        @if(!empty($mother->image))
                            <img src="{{ asset('storage/images/sponsorship') . $mother->image }}" alt="{{ $mother->names }}" class="w-100 sponsor-card__img">
                        @else
                            <div class="sponsor-card__img d-flex align-items-center justify-content-center bg-light text-muted">Photo coming soon</div>
                        @endif
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h3 class="h5 mb-1">{{ $mother->names }}</h3>
                            <p class="text-muted small mb-2">{{ $mother->age ? 'Age '.$mother->age : '' }} {{ $mother->address ? '· '.$mother->address : '' }}</p>
                            <p class="flex-grow-1">{{ \Illuminate\Support\Str::limit(strip_tags($mother->testimany ?? $mother->testimony ?? ''), 140, '…') }}</p>
                            <span class="badge {{ ($mother->status ?? '') === 'Not Sponsored' ? 'bg-warning text-dark' : 'bg-success' }} mb-3 align-self-start">
                                {{ $mother->status ?? 'Available' }}
                            </span>
                            <a href="{{ route('getInvolved') }}?way=donation#get-involved-form" class="tp-btn align-self-start">Get involved for {{ explode(' ', $mother->names)[0] ?? 'her' }}</a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">Sponsorship profiles will appear here once published by the admin team. You can still <a href="{{ route('getInvolved') }}?way=donation#get-involved-form">get involved through giving</a>.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
