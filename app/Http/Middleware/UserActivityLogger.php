<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;
use Symfony\Component\HttpFoundation\Response;

class UserActivityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            if ($request->is('storage/*') || $request->is('favicon.ico') || $request->is('admin/*')) {
                return;
            }

            if (
                $request->is('user/unread-messages-count') ||
                $request->is('user/notifications-count') ||
                $request->is('get-states') ||
                $request->is('user/get-user-activity/*') ||
                $request->is('user/roles/*')
            ) {
                return;
            }

            if (! $request->isMethod('GET')) {
                return;
            }

            $user = Auth::user();
            $ip = $request->ip();
            $countryCode = Helper::getVisitorCountryCode();
            $countryName = Helper::getVisitorCountryName();
            $url = $request->fullUrl();
            $ua = $request->header('User-Agent');

            $userRoles = $user ? implode(',', $user->getRoleNames()->toArray()) : null;

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
                'device_mac'         => null,
                'device_type'        => $ua,
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
