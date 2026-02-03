<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page');
        }

        $user = Auth::user();

        // Check if user has SUPER ADMIN role using UserType
        $isSuperAdmin = false;

        // Check using hasNewRole method
        if (method_exists($user, 'hasNewRole')) {
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');
        }

        // Fallback: check using getFirstUserRoleType (type == 1 is SUPER ADMIN)
        if (!$isSuperAdmin && method_exists($user, 'getFirstUserRoleType')) {
            $isSuperAdmin = ($user->getFirstUserRoleType() == 1);
        }

        if (!$isSuperAdmin) {
            abort(403, 'Access Denied. Only SUPER ADMIN can access this resource.');
        }

        return $next($request);
    }
}
