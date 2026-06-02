<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Restricts membership routes/APIs to all non-super-admin users.
 * IN_APP_MEMBERSHIP only affects the Flutter app (see membershipAppApplicable).
 */
class EnsureMemberSovereign
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || $user->hasNewRole('SUPER ADMIN')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Membership is not available for Super Admin accounts.',
                ], 403);
            }

            abort(403, 'Membership is not available for Super Admin accounts.');
        }

        return $next($request);
    }
}
