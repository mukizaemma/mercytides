@extends('layouts.frontbase')

@section('title', 'Where We Work')

@section('content')

@php
    use App\Support\MercyTidesContent;
    $locationHtml = MercyTidesContent::field($about->model_content ?? null, MercyTidesContent::whereWeWorkHtml());
@endphp

@include('frontend.includes.page-header', [
    'title' => 'Where We Work',
    'caption' => 'Serving unwed young mothers in Wakiso District and communities near Kampala, Uganda.',
    'pageKey' => 'model',
])

<section class="page-standalone grey-bg pt-60 pb-90">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-stretch">
            <div class="col-lg-6 order-2 order-lg-1">
                <article class="page-standalone-card h-100 d-flex flex-column">
                    <header class="page-standalone-card__head">
                        <span class="page-standalone-card__icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div>
                            <h2 class="page-standalone-card__title mb-0">Our location &amp; focus</h2>
                        </div>
                    </header>
                    <div class="page-standalone-card__body postbox__text flex-grow-1">{!! $locationHtml !!}</div>
                </article>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <figure class="page-standalone-media mb-0 h-100">
                    @if(!empty($about->model_image))
                        <div class="page-standalone-media__frame">
                            <img src="{{ asset('storage/images/' . $about->model_image) }}" alt="Mercy Tides in Uganda" class="page-standalone-media__img page-standalone-media__img--cover">
                        </div>
                    @elseif(!empty($about->image))
                        <div class="page-standalone-media__frame">
                            <img src="{{ asset('storage/images/' . $about->image) }}" alt="Mercy Tides Uganda" class="page-standalone-media__img page-standalone-media__img--cover">
                        </div>
                    @else
                        <div class="page-standalone-media__placeholder d-flex align-items-center justify-content-center text-center p-4 h-100">
                            <div>
                                <i class="fas fa-map-marked-alt d-block mb-3" style="font-size: 2.5rem; opacity: 0.35;"></i>
                                <p class="mb-1 fw-semibold">Wakiso District, Uganda</p>
                                <p class="small text-muted mb-0">Bunamwaya Parish · Makindye Sub County · Kyadondo County</p>
                            </div>
                        </div>
                    @endif
                </figure>
            </div>
        </div>
    </div>
</section>

@endsection
