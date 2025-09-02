<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use App\Mail\SendUserCodeResetPassword;
use App\Mail\SendUserNameMail;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    public function forgetPasswordShow()
    {
        return view('user.auth.forgot-password');
    }

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|exists:users,email',
        ]);
        // return $validator->errors();
        $count = User::where('email', $request->email)->count();
        if ($count > 0) {
            $user = User::where('email', $request->email)->select('id', 'email')->first();
            PasswordReset::where('email', $request->email)->delete();
            $id = Crypt::encrypt($user->id);
            $token = Str::random(20) . 'pass' . $user->id;
            PasswordReset::create([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            $details = [
                'id' => $id,
                'token' => $token
            ];

            Mail::to($request->email)->send(new SendUserCodeResetPassword($details));
            return redirect()->back()->with('message', "Please! check your mail to reset your password.");
        } else {
            return redirect()->back()->with('error', "Couldn't find your account!");
        }
    }

    public function resetPassword($id, $token)
    {
        // return "dfs";
        $user = User::findOrFail(Crypt::decrypt($id));
        $resetPassword = PasswordReset::where('email', $user->email)->first();
        $newTime =  date('h:i A', strtotime($resetPassword->created_at->addHour()));

        if ($resetPassword->token == $token && $resetPassword->created_at->addHour() > Carbon::now()) {
            $id = $id;
            return view('user.auth.reset-password')->with(compact('id'));
        } else {
            return redirect()->route('login')->with('error', 'Link has been expired!');
        }
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'required|min:8|same:password'
        ],[
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);
        // return $request->all();
        try {
            if ($request->id != '') {
                $id = Crypt::decrypt($request->id);
                User::where('id', $id)->update(['password' => bcrypt($request->password)]);
                $now_time = Carbon::now()->toDateTimeString();
                return redirect()->route('home')->with('message', 'Password has been changed successfully.');
            } else {
                abort(404);
            }
        } catch (\Throwable $th) {
            return redirect()->route('home')->with('message', 'Something went wrong.');
        }
    }

    public function forgetUsernameShow()
    {
        return view('user.auth.forgot-username');
    }

    public function forgetUsername(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        $phone_number = $request->full_phone_number;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->first();


        if ($check) {
            PasswordReset::where('email', $check->email)->delete();
            $token = Str::random(20) . 'pass' . $check->id;
            PasswordReset::create([
                'email' => $check->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            $details = [
                'id' => Crypt::encrypt($check->id),
                'token' => $token
            ];

            Mail::to($check->email)->send(new SendUserNameMail($check, $details));
            return redirect()->route('forget-username-confirmation', ['id' => Crypt::encrypt($check->id)]);
        } else {
            return redirect()->back()->with('error', 'Phone number not found!');
        }
    }

    public function confirmationEmail($id)
    {
        $user = User::findOrFail(Crypt::decrypt($id));
        return view('user.auth.confirmation-email')->with(compact('user'));
    }

    // public function resetUsername($id, $token)
    // {
    //     $user = User::findOrFail(Crypt::decrypt($id));
    //     $resetPassword = PasswordReset::where('email', $user->email)->first();
    //     $newTime =  date('h:i A', strtotime($resetPassword->created_at->addHour()));

    //     if ($resetPassword->token == $token && $resetPassword->created_at->addHour() > Carbon::now()) {
    //         $id = $id;
    //         return view('user.auth.reset-username')->with(compact('id'));
    //     } else {
    //         return redirect()->route('login')->with('error', 'Link has been expired!');
    //     }
    // }

    // public function changeUsername(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string|max:255|unique:users,user_name',
    //         'confirm_username' => 'required|string|max:255|same:username'
    //     ]);

    //     try {
    //         if ($request->id != '') {
    //             $id = Crypt::decrypt($request->id);
    //             // dd($id);
    //             User::where('id', $id)->update(['user_name' => $request->username]);
    //             return redirect()->route('home')->with('message', 'Username has been changed successfully.');
    //         } else {
    //             abort(404);
    //         }
    //     } catch (\Throwable $th) {
    //         return redirect()->route('home')->with('message', 'Something went wrong.');
    //     }
    // }
}
