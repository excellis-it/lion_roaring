<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MemberAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow unauthenticated requests to proceed (auth middleware handles auth)
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $sub = $user->userLastSubscription ?? null;

        // If the user has a subscription and it is expired, redirect them to membership page
        if (auth()->check() && auth()->user()->hasNewRole('MEMBER_NON_SOVEREIGN')) {
            if ($sub && $sub->subscription_expire_date) {


                $expire = \Carbon\Carbon::parse($sub->subscription_expire_date);
                if ($expire->isPast()) {
                    // Allow membership routes and logout so user can renew or logout
                    if ($request->is('user/membership') || $request->is('user/membership/*') || $request->is('user/logout')) {
                        return $next($request);
                    }

                    return redirect()->route('user.membership.index')->with('error', 'Your membership has expired. Please renew to reactivate your account.');
                }
            } else {
                if ($request->is('user/membership') || $request->is('user/membership/*') || $request->is('user/logout')) {
                    return $next($request);
                }
                return redirect()->route('user.membership.index')->with('error', 'You need an active membership to access this section. Please subscribe.');
            }
        }

        return $next($request);
    }
}
