<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Loaded here (not via composer.json "files") so production deploys
        // do not require composer dump-autoload / composer update.
        require_once app_path('helpers.php');
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
                $key = trim((string) ($settings->STRIPE_KEY ?? ''));
                $secret = trim((string) ($settings->STRIPE_SECRET ?? ''));

                // Both halves must be present and in the same mode. A half-configured or
                // mixed pair mints the card token on one Stripe account and charges on
                // another, which surfaces as "No such token" at checkout. Keep .env instead.
                $mode = static fn (string $k): ?string => str_contains($k, '_live_')
                    ? 'live'
                    : (str_contains($k, '_test_') ? 'test' : null);

                if ($key !== '' && $secret !== '' && $mode($key) !== null && $mode($key) === $mode($secret)) {
                    config([
                        'services.stripe.key' => $key,
                        'services.stripe.secret' => $secret,
                    ]);
                } elseif ($key !== '' || $secret !== '') {
                    Log::warning('Ignoring site_settings Stripe keys: incomplete or mixed live/test pair.', [
                        'key_mode' => $key !== '' ? $mode($key) : null,
                        'secret_mode' => $secret !== '' ? $mode($secret) : null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during migrations / when DB is unavailable.
        }
    }
}
