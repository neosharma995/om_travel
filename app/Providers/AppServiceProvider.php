<?php

namespace App\Providers;

use App\Models\GalleryItem;
use App\Models\SiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(Str::transliterate(
                Str::lower($request->string('email')->toString()).'|'.$request->ip()
            ));
        });

        View::composer(['layouts.website', 'website.*', 'layouts.customer', 'auth.*'], function (BladeView $view): void {
            $siteSettings = SiteSetting::defaults();
            $footerGallery = collect();

            if (Schema::hasTable('site_settings')) {
                $siteSettings = SiteSetting::allKeyed();
            }

            if (Schema::hasTable('gallery_items')) {
                $footerGallery = GalleryItem::query()
                    ->active()
                    ->ordered()
                    ->limit(6)
                    ->get();
            }

            $view->with([
                'siteSettings' => $siteSettings,
                'footerGallery' => $footerGallery,
            ]);
        });
    }
}
