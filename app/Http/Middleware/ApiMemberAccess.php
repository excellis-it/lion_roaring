<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiMemberAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('lion_roaring.in_app_membership')) {
            return $next($request);
        }

        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if (!$user->isMemberSovereign()) {
            return $next($request);
        }

        if ($user->userLastSubscription != null) {
            if ($user->userLastSubscription->subscription_expire_date >= date('Y-m-d')) {
                return $next($request);
            }

            return response()->json([
                'message' => 'Your subscription has been expired. Please renew your subscription.',
                'status' => false,
            ], 202);
        }

        return response()->json([
            'message' => 'You have not subscribed to any plan. Please subscribe to a plan.',
            'status' => false,
        ], 202);
    }
}
