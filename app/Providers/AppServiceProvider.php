<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Partner;
use App\Models\ProductStoryPoint;
use App\Models\ProductStorySetting;
use App\Models\Program;
use App\Models\Service;
use App\Models\Setting;
use App\Services\FormSubmissionService;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ImageUploadService::class);
    }


    public function boot()
    {
        // Keep generated URLs on the host the user is actually visiting when APP_URL drifts
        // (common on shared/multi-site setups) so admin form posts do not 404 elsewhere.
        if (! $this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            $root = request()->getSchemeAndHttpHost() . request()->getBasePath();
            if ($root !== '') {
                URL::forceRootUrl(rtrim($root, '/'));
            }
            if (request()->isSecure()) {
                URL::forceScheme('https');
            }
        }

        UploadedFile::macro('storeOptimized', function (string $directory, string $disk = 'public', array $options = []) {
            return app(ImageUploadService::class)->store($this, $directory, $disk, $options);
        });

        UploadedFile::macro('storeOptimizedAs', function (string $directory, string $filename, string $disk = 'public', array $options = []) {
            return app(ImageUploadService::class)->storeAs($this, $directory, $filename, $disk, $options);
        });

        View::share(
            'setting',
            Schema::hasTable('settings')
                ? Setting::firstOrEmpty()
                : new Setting()
        );
        View::share(
            'ourPrograms',
            Schema::hasTable('programs')
                ? Program::query()->oldest()->get()
                : collect()
        );
        View::share(
            'menuServices',
            Schema::hasTable('services')
                ? Service::query()->active()->orderBy('sort_order')->orderBy('title')->get()
                : collect()
        );
        View::share(
            'partners',
            Schema::hasTable('partners')
                ? Partner::query()->latest()->get()
                : collect()
        );

        View::composer('layouts.frontbase', function ($view) {
            $service = app(FormSubmissionService::class);
            $view->with('recaptchaSiteKey', $service->recaptchaSiteKey());
        });

        View::composer(['frontend.our-products', 'frontend.product-detail'], function ($view) {
            if (! Schema::hasTable('product_story_points')) {
                $view->with([
                    'productStoryHeading' => null,
                    'productStoryPoints' => collect(),
                ]);

                return;
            }

            $heading = null;
            if (Schema::hasTable('product_story_settings')) {
                $row = ProductStorySetting::query()->first();
                $heading = $row?->heading;
            }

            $points = ProductStoryPoint::query()->active()->ordered()->get();

            $view->with([
                'productStoryHeading' => $heading,
                'productStoryPoints' => $points,
            ]);
        });
    }
}
