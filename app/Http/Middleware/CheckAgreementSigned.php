<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserRegisterAgreement;

class CheckAgreementSigned
{
    /**
     * Handle an incoming request.
     *
     * Redirect authenticated users who have NOT signed the PMA agreement
     * (missing signature or UserRegisterAgreement record) to the signing page.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            // Super Admins are exempt from agreement signing
            $isSuperAdmin = false;
            if (method_exists($user, 'hasNewRole')) {
                $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');
            }
            if (!$isSuperAdmin && method_exists($user, 'getFirstUserRoleType')) {
                $isSuperAdmin = ($user->getFirstUserRoleType() == 1);
            }
            if ($isSuperAdmin) {
                return $next($request);
            }

            $hasSignature = !empty($user->signature);
            $hasAgreement = UserRegisterAgreement::where('user_id', $user->id)->exists();

            if (!$hasSignature || !$hasAgreement) {
                // Allow access to the agreement signing routes and logout to avoid redirect loop
                $allowedRoutes = [
                    'user.sign.agreement',
                    'user.sign.agreement.submit',
                    'user.sign.agreement.preview',
                    'logout',
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('user.sign.agreement');
                }
            }
        }

        return $next($request);
    }
}
