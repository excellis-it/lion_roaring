<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;
use App\Services\MarketRateService;


class UserActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Exclude some system routes
            if ($request->is('storage/*') || $request->is('favicon.ico') || $request->is('admin/*')) {
                return $response;
            }

            // exclude some routes - FIXED: use wildcard pattern correctly
            if (
                $request->is('user/unread-messages-count') ||
                $request->is('user/notifications-count') ||
                $request->is('get-states') ||
                $request->is('user/get-user-activity/*') ||
                $request->is('user/roles/*')
            ) {
                return $response;
            }

            // Skip non-GET requests (e.g. POST forms)
            if (!$request->isMethod('get')) {
                return $response;
            }

            // Refresh market rates at most every 12 hours
            MarketRateService::refreshIfStale();

            $user = Auth::user();
            $ip = $request->ip();
            $countryCode = Helper::getVisitorCountryCode();
            $countryName = Helper::getVisitorCountryName();
            $url = $request->fullUrl();
            $ua = $request->header('User-Agent');

            $userRoles = $user ? implode(',', $user->getRoleNames()->toArray()) : null;

            // $permissionAccess = $user ? implode(',', $user->getAllPermissions()->pluck('name')->toArray()) : null;

            // only requested perissions for this route
            $route = $request->route();
            $permissionAccess = null;
            if ($route) {
                $action = $route->getAction();
                if (isset($action['middleware'])) {
                    $middlewares = is_array($action['middleware']) ? $action['middleware'] : explode(',', $action['middleware']);
                    $permissions = [];
                    foreach ($middlewares as $middleware) {
                        if (str_starts_with($middleware, 'permission:')) {
                            $permString = substr($middleware, strlen('permission:'));
                            $permParts = explode('|', $permString);
                            $permissions = array_merge($permissions, $permParts);
                        }
                    }
                    $permissionAccess = implode(',', $permissions);
                }
            }

            // Detect basic device/mac info (MAC not available from browser, optional)
            $deviceMac = null;
            // $deviceType = $this->detectDevice($ua);
            $deviceType = request()->header('User-Agent');

            // activityType should extract path or route name with human readable format
            $path = trim($request->path(), '/');
            $activityType = $path ? strtoupper(str_replace('/', '_', $path)) : 'WEBSITE_VISIT';

            UserActivity::create([
                'user_id'            => $user?->id ?? null,
                'user_name'          => $user ? ($user->first_name . ' ' . $user->last_name) : 'Guest',
                'email'              => $user?->email ?? '-',
                'user_roles'         => $userRoles ?? '-',
                'ecclesia_name'      => optional($user?->ecclesia)->name ?? '-',
                'ip'                 => $ip ?? '-',
                'country_code'       => $countryCode,
                'country_name'       => $countryName,
                'device_mac'         => $deviceMac,
                'device_type'        => $deviceType,
                'browser'            => $this->getBrowserName($ua),
                'url'                => substr($url, 0, 1000),
                'permission_access'  => $permissionAccess,
                'activity_type'      => $activityType ?? '-',
                'activity_description' => substr('Visited: ' . $url, 0, 1000),
                'activity_date'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Auto user activity log failed: ' . $e->getMessage());
        }

        return $response;
    }

    private function detectDevice($ua)
    {
        if (preg_match('/mobile/i', $ua)) return 'Mobile';
        if (preg_match('/tablet/i', $ua)) return 'Tablet';
        return 'Desktop';
    }

    private function getBrowserName($ua)
    {
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'Chrome') !== false) return 'Chrome';
        if (strpos($ua, 'Safari') !== false) return 'Safari';
        if (strpos($ua, 'Edge') !== false) return 'Edge';
        if (strpos($ua, 'Opera') !== false) return 'Opera';
        return 'Unknown';
    }
}
