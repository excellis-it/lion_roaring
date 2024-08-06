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
        if (auth()->check() && isset(auth()->user()->userSubscription) && auth()->user()->userLastSubscription != null) {
            if (auth()->user()->userLastSubscription->subscription_expire_date >= date('Y-m-d')) {
                return $next($request);
            } else { 
                if (auth()->check() && auth()->user()->hasRole('MEMBER')) {
                    return redirect()->route('user.subscription')->with('error', 'Your subscription has been expired. Please renew your subscription.');
                } else {
                    return $next($request);
                }
            }
        } else {
            if (auth()->check() && auth()->user()->hasRole('MEMBER')) {
                return redirect()->route('user.subscription')->with('error', 'You have not subscribed to any plan. Please subscribe to a plan.');
            } else {
                return $next($request);
            }
        }
    }
}
