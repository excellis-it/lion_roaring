<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        $encryptedOtp = Session::get('otp');
        if ($encryptedOtp && Crypt::decrypt($encryptedOtp) == $request->otp) {
            $userId = Session::get('user_id');
            $user = User::find($userId);

            if ($user) {
                Auth::login($user);
                Session::forget('otp');
                Session::forget('user_id');
                return response()->json(['message' => 'OTP verified successfully', 'status' => true, 'redirect' => route('user.profile')]);
            } else {
                return response()->json(['message' => 'User not found', 'status' => false]);
            }
        } else {
            return response()->json(['message' => 'Invalid OTP', 'status' => false]);
        }
    }
}
