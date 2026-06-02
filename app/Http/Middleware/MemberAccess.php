<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if (!$user instanceof User) {
            return $next($request);
        }

        if ($user->hasNewRole('SUPER ADMIN')) {
            return $next($request);
        }

        if ($request->is('user/membership', 'user/membership/*', 'user/logout')) {
            return $next($request);
        }

        try {
            $sub = $user->userLastSubscription;

            if ($sub && $sub->subscription_expire_date) {
                if (Carbon::parse($sub->subscription_expire_date)->isPast()) {
                    return redirect()->route('user.membership.index')
                        ->with('error', 'Your membership has expired. Please renew to reactivate your account.');
                }

                return $next($request);
            }

            return redirect()->route('user.membership.index')
                ->with('error', 'You need an active membership to access this section. Please subscribe.');
        } catch (\Throwable $e) {
            Log::error('MemberAccess middleware error for user ' . $user->id . ': ' . $e->getMessage());
        }

        return $next($request);
    }
}
