<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\AccountPendingApprovalMail;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\RegisterAgreement;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        ], [
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
                session()->flash('message', 'Login success');
                return response()->json(['message' => 'Login success', 'status' => true, 'redirect' => route('user.profile')]);
            } else {
                auth()->logout();
                return response()->json(['message' => 'Your account is not active!', 'status' => false]);
            }
        } else {
            return response()->json(['message' => 'User name & password was invalid!', 'status' => false]);
        }
    }

    public function register()
    {
        $eclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
        $countries = Country::all();
        return view('user.auth.register')->with(compact('eclessias', 'countries'));
    }

    public function registerCheck(Request $request)
    {
        // dd($request->all());
        // $request->validate([
        //     'user_name' => 'required|string|max:255|unique:users',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'ecclesia_id' => 'nullable',
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'middle_name' => 'nullable|string|max:255',
        //     'address' => 'required|string|max:255',
        //     'phone_number' => 'required',
        //     'city' => 'required|string|max:255',
        //     'state' => 'required|string|max:255',
        //     'address2' => 'nullable|string|max:255',
        //     'country' => 'required|string|max:255',
        //     'zip' => 'required',
        //     'email_confirmation' => 'required|same:email',
        //     'password' => 'required|string|regex:/^[\S]+$/',
        //     'password_confirmation' => 'required|same:password',
        // ]);

        $validator = Validator::make($request->all(), [
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
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $phone_number = $request->full_phone_number;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return redirect()->back()->withErrors(['phone_number' => 'Phone number already exists'])->withInput();
        }

        $uniqueNumber = rand(1000, 9999);
        $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';

        $user = new User();
        $user->user_name = $request->user_name;
        $user->ecclesia_id = $request->ecclesia_id;
        $user->email = $request->email;
        $user->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone_number : $request->phone_number;
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
        $maildata = [
            'name' => $request->full_name,
        ];

        Mail::to($request->email)->send(new AccountPendingApprovalMail($maildata));
        return redirect()->route('home')->with('message', 'Plase wait for admin approval');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('home')->with('message', 'Logout success');
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country)->get();
        return response()->json($states);
    }
}
