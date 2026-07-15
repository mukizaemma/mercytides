@extends('layouts.frontbase')

@section('title', 'Mothers in our program')

@section('content')
    @include('frontend.includes.page-header', [
        'title' => 'Mothers in our program',
        'pageKey' => 'sponsor_young_mother',
    ])

    <section class="mothers-gallery-page pt-90 pb-90">
        <div class="container">
            @include('frontend.includes.mothers-gallery', [
                'mothers' => $mothers ?? collect(),
                'limit' => 1000,
                'showSectionHeader' => true,
                'embedded' => true,
                'sectionTitle' => 'Mothers in our program',
                'sectionLead' => 'Meet the young mothers Mercy Tides walks alongside — each portrait is a story of resilience, faith, and new beginnings.',
                'showViewMore' => false,
            ])

            @if(($mothers ?? collect())->isEmpty())
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0">Mother profiles will appear here once photos are published.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
