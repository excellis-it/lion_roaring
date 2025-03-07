<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\VerifyOTP;

class EmailVerificationController extends Controller
{
    public function sendOtp(Request $request)
    {
        // This method is not needed as OTP is sent during loginCheck
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
        ]);

        if (!$request->session()->has('user_id')) {
            return response()->json(['message' => 'User not found', 'status' => false]);
        }

        $userId = Session::get('user_id');

        $user = User::find($userId);

        $verify_otp = VerifyOTP::where('user_id', $userId)->orderBy('id', 'desc')->first();

        if (!$verify_otp || $verify_otp->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP', 'status' => false]);
        }

        $verify_otp->delete();

        Auth::login($user);
        Session::forget('user_id');
        return response()->json(['message' => 'OTP verified successfully', 'status' => true, 'redirect' => route('user.profile')]);
    }
}
