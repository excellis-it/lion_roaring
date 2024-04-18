<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *  @group Authentication
 */
class AuthController extends Controller
{
    protected $successStatus = 200;

    /**
     * Login
     *
     * @bodyParam user_name string required The username or email of the user. Example: john_doe
     * @bodyParam password string required The password of the user. Example: password
     *
     * @response 200{
     * "token": "dsdsdsd"
     * "status": true
     * "message": "Login successful"
     * }
     */

    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'user_name' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $fieldType = filter_var($request->user_name, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

            if (Auth::attempt([$fieldType => $request->user_name, 'password' => $request->password])) {
                $user = User::where($fieldType, $request->user_name)->first();
                if ($user->status == 1) {
                    $token = $user->createToken('authToken')->accessToken;
                    return response()->json(['token' => $token, 'status' => true, 'message' => 'Login successful'], 200);
                } else {
                    auth()->logout();
                    return response()->json(['message' => 'Your account is not active!', 'status' => false], 201);
                }
            } else {
                return response()->json(['message' => 'Unauthorised', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Register
     */

    public function register(Request $request)
    {
        $validator = validator($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'nullable|numeric',
            'email_confirmation' => 'required|same:email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $user = new User();
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->middle_name = $request->middle_name;
            $user->address = $request->address;
            $user->phone = $request->phone_number;
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json(['message' => 'User created successfully', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }
}
