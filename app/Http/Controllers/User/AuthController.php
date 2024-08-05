<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\RegisterAgreement;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login()
    {
        $agreement = RegisterAgreement::orderBy('id', 'desc')->first();

        return view('user.auth.login')->with(compact('agreement'));
    }

    public function loginCheck(Request $request)
    {
        $request->validate([
            'user_name'    => 'required',
            'password' => 'required|min:8'
        ],[
            'user_name.required' => 'User name or email is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters'
        ]);

        // login by user_name or email
        $fieldType = filter_var($request->user_name, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
        $request->merge([$fieldType => $request->user_name]);
        // return $fieldType;
        if (auth()->attempt($request->only($fieldType, 'password'))) {
            if (auth()->user()->status == 1) {
                return redirect()->route('user.profile');
            } else {
                auth()->logout();
                return redirect()->back()->with('error', 'Your account is not active!');
            }
        } else {
            return redirect()->back()->with('error', 'User name & password was invalid!');
        }
    }

    public function register()
    {
        $eclessias = Ecclesia::orderBy('id', 'desc')->get();
        $countries = Country::all();
        return view('user.auth.register')->with(compact('eclessias', 'countries'));
    }

    public function registerCheck(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'ecclesia_id' => 'nullable',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'zip' => 'required',
            'email_confirmation' => 'required|same:email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = new User();
        $user->user_name = $request->user_name;
        $user->ecclesia_id = $request->ecclesia_id;
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->phone = $request->country_code ? '+'.$request->country_code . ' ' . $request->phone_number : $request->phone_number;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->address2 = $request->address2;
        $user->country = $request->country;
        $user->zip = $request->zip;
        $user->password = bcrypt($request->password);
        $user->email_verified_at = now();
        $user->status = 0;
        $user->save();

        $user->assignRole('MEMBER');
        return redirect()->route('login')->with('message', 'User registered successfully');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country)->get();
        return response()->json($states);
    }
}
