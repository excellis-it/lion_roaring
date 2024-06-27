<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendUserCodeResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
/**
 * @group Forget Password
 */
class ForgetPasswordController extends Controller
{
    protected $successStatus = 200;
    /**
     * Forget Password
     * @bodyParam email string required The email of the user. Example:
     * @response 200 {
     * "status": true,
     * "message": "Password reset link sent to your email"
     * }
     */

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 201);
        }

        $count = User::where('email', $request->email)->role('MEMBER')->count();
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
            return response()->json(['status' => true, 'message' => 'Password reset link sent to your email'], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => 'Email not found'], 201);
        }
    }
}
