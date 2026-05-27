<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Restricts member self-service membership routes/APIs to MEMBER_SOVEREIGN users.
 * IN_APP_MEMBERSHIP only affects the Flutter app (see membershipAppApplicable).
 */
class EnsureMemberSovereign
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->isMemberSovereign()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Membership is only available for Member Sovereign accounts.',
                ], 403);
            }

            abort(403, 'Membership is only available for Member Sovereign accounts.');
        }

        return $next($request);
    }
}
