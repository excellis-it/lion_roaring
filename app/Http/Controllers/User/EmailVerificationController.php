<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\VerifyOTP;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    public function sendOtp(Request $request)
    {
        // This method is not needed as OTP is sent during loginCheck
    }

    public function resendOtp(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return response()->json(['message' => 'User session expired', 'status' => false]);
        }

        $userId = Session::get('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => false]);
        }

        // Check if last OTP was sent within the last 10 minutes
        $lastOtp = VerifyOTP::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastOtp && Carbon::parse($lastOtp->created_at)->addMinutes(10)->gt(Carbon::now())) {
            $timeLeft = Carbon::now()->diffInSeconds(Carbon::parse($lastOtp->created_at)->addMinutes(10));
            return response()->json([
                'message' => 'Please wait before requesting another OTP',
                'status' => false,
                'time_left' => $timeLeft
            ]);
        }

        // Generate new OTP
        $otp = rand(1000, 9999);
        $otp_verify = new VerifyOTP();
        $otp_verify->user_id = $user->id;
        $otp_verify->email = $user->email;
        $otp_verify->otp = $otp;
        $otp_verify->save();

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['message' => 'OTP sent to your email', 'status' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Email server temporary unavailable. Please try later.', 'status' => false]);
        }
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
