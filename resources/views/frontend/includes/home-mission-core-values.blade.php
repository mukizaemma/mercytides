@include('frontend.includes.objectives-grid', [
    'sectionClass' => 'home-mv-strip mt-objectives--on-grey',
    'captionKey' => 'objectives.caption_home',
])

<section class="pb-4 grey-bg">
    <div class="container pb-3">
        <div class="row">
            <div class="col-12 text-center wow tpfadeUp reveal-on-scroll" data-wow-duration=".9s" data-wow-delay=".1s">
                <a href="{{ route('ourMission') }}" class="tp-btn">{{ \App\Support\SiteContent::text('home.objectives.more_btn') }}</a>
            </div>
        </div>
    </div>
</section>
