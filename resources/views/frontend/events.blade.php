@extends('layouts.frontbase')

@section('title', 'Home Page')

@section('content')


        @include('frontend.includes.page-header', [
            'title' => \App\Support\SiteContent::text('events.page_title'),
            'pageKey' => 'events',
        ])

            @include('frontend.includes.events')

        <!-- cta-area-start -->
            @include('frontend.includes.backImage')
        <!-- cta-area-end -->


@endsection
