<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

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
        Paginator::useBootstrap();

        // Force HTTPS scheme behind reverse proxies (demo / AWS).
        // Do NOT forceRootUrl — production and demo serve global + regional from one
        // codebase (different host or path); URL root must follow the current request.
        $appUrl = (string) config('app.url', '');
        if (str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1200)->by(optional($request->user())->id ?: $request->ip());
        });

        // Let admin-managed Stripe keys (site_settings) override the .env values
        // so every consumer reading config('services.stripe.*') stays in sync.
        try {
            if (Schema::hasTable('site_settings')) {
                $settings = SiteSetting::first();
                if ($settings && !empty($settings->STRIPE_SECRET)) {
                    config([
                        'services.stripe.key' => $settings->STRIPE_KEY,
                        'services.stripe.secret' => $settings->STRIPE_SECRET,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during migrations / when DB is unavailable.
        }
    }
}
