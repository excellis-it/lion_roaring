<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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
