<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

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
