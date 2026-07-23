<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class EnsureCanonicalCountryUrl
{
    /**
     * Redirect country paths to canonical host.
     * org/in → us/in, org/us → us, us/in stays, countries with domain → their domain.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if ($request->filled('cc')) {
            $cleanUrl = Helper::consumeVisitorCountryQueryParam($request);
            if ($cleanUrl !== null) {
                return redirect()->to($cleanUrl, 302);
            }
        }

        if (trim($request->path(), '/') === '' && Helper::isGlobalInstance()) {
            $sessionRedirect = Helper::resolveSessionCountryRedirectOnGlobalRoot();
            if ($sessionRedirect) {
                return redirect()->away($sessionRedirect, 302);
            }
        }

        if (trim($request->path(), '/') === '' && Helper::isDefaultRegionalInstance()) {
            $sessionRedirect = Helper::resolveSessionCountryRedirectOnRegionalRoot();
            if ($sessionRedirect) {
                return redirect()->away($sessionRedirect, 302);
            }
        }

        $pathCode = Helper::extractCountryCodeFromPath($request->path());
        if (!$pathCode) {
            return $next($request);
        }

        $redirectUrl = Helper::resolveCanonicalRedirectForPathCountry($pathCode);
        if ($redirectUrl) {
            return redirect()->away($redirectUrl, 302);
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is(
            'api/*',
            'admin',
            'admin/*',
            'storage/*',
            'set-visitor-country',
        );
    }
}
