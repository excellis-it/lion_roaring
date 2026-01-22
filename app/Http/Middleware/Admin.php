<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Admin
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
        if (Auth::check()) {
            $user = Auth::user();
            $superAdmin = ($user->getFirstUserRoleType() && $user->getFirstUserRoleType() == 1 && $user->status == 1) ? true : false;
            if ($superAdmin) {
                return $next($request);
            } else {
                return redirect()->route('admin.login')->with('error', 'You are not authorized to access this page');
            }
        } else {
            return redirect()->route('admin.login')->with('error', 'You are not authorized to access this page');
        }
    }
}
