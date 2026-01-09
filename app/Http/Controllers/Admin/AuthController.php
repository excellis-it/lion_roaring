<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        // if (Auth::check()) {
        //     return redirect()->route('admin.dashboard');
        // } else {
        return view('admin.auth.login');
        // }
    }

    public function redirectAdminLogin()
    {
        return redirect()->route('admin.login');
    }

    public function loginCheck(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => 'required|min:8'
        ]);
        $remember_me = $request->has('remember_me') ? true : false;
        if (!$request->time_zone) {
            return redirect()->back()->with('error', 'Something went wrong with the timezone detection. Please refresh the page and try again.');
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {
            $user = User::where('email', $request->email)->first();
            // dd($user);
            if ($user->getFirstUserRoleType() && $user->getFirstUserRoleType() != 1) {
                return redirect()->back()->with('error', 'This User Not Allowed Here!');
            }
            if ($user->getFirstUserRoleType() && $user->getFirstUserRoleType() == 1 && $user->status == 1) {
                $user->update(['time_zone' => $request->time_zone]);
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Email id & password was invalid!');
            }
        } else {
            return redirect()->back()->with('error', 'Email id & password was invalid!');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
