<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class User
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
        if (auth()->check() && auth()->user()->status == 1) {
            $user = auth()->user()->roles()->whereIn('type', [1, 2, 3])->first();
            if ($user) {
                return $next($request);
            } else {
                return redirect()->route('home')->with('error', 'You must be logged in to access this page');
            }
        }
        return redirect()->route('home')->with('error', 'You must be logged in to access this page');
    }
}
