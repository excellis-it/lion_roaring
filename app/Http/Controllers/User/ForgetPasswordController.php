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
        $count = User::where('email', $request->email)->role('CUSTOMER')->count();
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
        $newTime =  date('h:i A', strtotime( $resetPassword->created_at->addHour()));

        if ($newTime > date('h:i A')) {

            $id = $id;
            return view('user.auth.reset-password')->with(compact('id'));
        } else {
            abort(404);
        }


    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password'
        ]);
        // return $request->all();
        try {
            if ($request->id != '') {
                $id = Crypt::decrypt($request->id);
                User::where('id', $id)->update(['password' => bcrypt($request->password)]);
                $now_time = Carbon::now()->toDateTimeString();
                return redirect()->route('login')->with('message', 'Password has been changed successfully.');
            } else {
                abort(404);
            }
        }
        catch (\Throwable $th) {
            return redirect()->route('login')->with('message', 'Something went wrong.');
        }

    }
}
