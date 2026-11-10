@extends('layouts.frontbase')

@section('title', \App\Support\SiteContent::text('mothers.page_title'))

@section('content')
    @include('frontend.includes.page-header', [
        'title' => \App\Support\SiteContent::text('mothers.page_title'),
        'pageKey' => 'sponsor_young_mother',
    ])

    <section class="mothers-gallery-page pt-90 pb-90">
        <div class="container">
            @include('frontend.includes.mothers-gallery', [
                'mothers' => $mothers ?? collect(),
                'limit' => 1000,
                'showSectionHeader' => true,
                'embedded' => true,
                'sectionTitle' => \App\Support\SiteContent::text('mothers.page_title'),
                'sectionLead' => \App\Support\SiteContent::text('mothers.page_lead'),
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
