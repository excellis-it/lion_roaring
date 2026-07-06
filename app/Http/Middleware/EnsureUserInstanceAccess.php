<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserInstanceAccess
{
    /**
     * Log out when user type or sign-in domain does not match the current site.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user instanceof User || $user->hasNewRole('SUPER ADMIN')) {
            return $next($request);
        }

        if (Helper::userHasValidInstanceSession($user)) {
            return $next($request);
        }

        $redirectUrl = Helper::resolveUserInstanceRedirectUrl($user);
        $message = Helper::userInstanceLogoutMessage($user);

        Auth::logout();
        Helper::clearBrowsingSession();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => $message,
                'redirect_url' => $redirectUrl,
                'logged_out' => true,
            ], 401);
        }

        if ($redirectUrl && !$this->isCurrentRequestUrl($request, $redirectUrl)) {
            $redirectUrl = $this->appendCountryHandoffToRedirectUrl($redirectUrl, $user);
            $separator = str_contains($redirectUrl, '?') ? '&' : '?';

            return redirect()->away($redirectUrl . $separator . 'instance_error=' . urlencode($message));
        }

        return redirect()->route('home')->with('error', $message);
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is(
            'user/logout',
            'logout',
            'login',
            'login-check',
            'verify-otp',
            'resend-otp',
            'send-otp',
            'register',
            'register/*',
            'set-visitor-country',
            'admin',
            'admin/*',
            'admin-login-check'
        );
    }

    private function isCurrentRequestUrl(Request $request, string $url): bool
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        if (!$host || $request->getHost() !== $host) {
            return false;
        }

        $port = $parsed['port'] ?? null;
        if ($port !== null) {
            return (string) $request->getPort() === (string) $port;
        }

        return in_array($request->getPort(), [80, 443], true);
    }

    private function appendCountryHandoffToRedirectUrl(string $url, User $user): string
    {
        $mainHost = parse_url(Helper::getMainUrl(), PHP_URL_HOST);
        $targetHost = parse_url($url, PHP_URL_HOST);
        $targetPath = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $isGlobalRoot = $mainHost
            && $targetHost
            && strtolower($targetHost) === strtolower($mainHost)
            && $targetPath === '';

        if ($isGlobalRoot && in_array($user->user_type, ['Global', 'G_R'], true)) {
            return Helper::appendCountryCodeQueryParam($url, 'GL');
        }

        if ($user->country) {
            $country = \App\Models\Country::find($user->country);
            if ($country) {
                return Helper::appendCountryCodeQueryParam($url, $country->code);
            }
        }

        return $url;
    }
}
