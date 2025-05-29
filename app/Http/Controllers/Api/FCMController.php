<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FCMService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @group FCM
 * @authenticated
 */
class FCMController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Update FCM Token
     *
     * Updates the FCM token for the authenticated user's device.
     *
     * @bodyParam fcm_token string required The FCM token from the client device. Example: "dGhpcyBpcyBhIGZha2UgdG9rZW4"
     *
     * @response 200 {
     *   "message": "FCM token updated successfully.",
     *   "status": true
     * }
     * @response 422 {
     *   "message": "Validation error.",
     *   "errors": {
     *     "fcm_token": ["The fcm token field is required."]
     *   }
     * }
     */
    public function updateToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            $user = Auth::user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            // Validate the token with Firebase
            $isValid = $this->fcmService->validateToken($request->fcm_token);

            if (!$isValid) {
                Log::warning('Invalid FCM token provided', [
                    'user_id' => $user->id,
                    'token' => $request->fcm_token
                ]);
            }

            return response()->json([
                'message' => 'FCM token updated successfully.',
                'status' => true,
                'token_valid' => $isValid
            ], 200);
        } catch (\Exception $e) {
            Log::error('FCM token update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update FCM token.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove FCM Token
     *
     * Removes the FCM token for the authenticated user (useful for logout).
     *
     * @response 200 {
     *   "message": "FCM token removed successfully.",
     *   "status": true
     * }
     */
    public function removeToken(Request $request)
    {
        try {
            $user = Auth::user();
            $user->fcm_token = null;
            $user->save();

            return response()->json([
                'message' => 'FCM token removed successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('FCM token removal failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to remove FCM token.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Test Notification
     *
     * Sends a test notification to the authenticated user's device.
     *
     * @bodyParam title string required The notification title. Example: "Test Notification"
     * @bodyParam body string required The notification body. Example: "This is a test message"
     *
     * @response 200 {
     *   "message": "Test notification sent successfully.",
     *   "status": true
     * }
     * @response 400 {
     *   "message": "No FCM token found for user.",
     *   "status": false
     * }
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:500'
            ]);

            $user = Auth::user();

            if (!$user->fcm_token) {
                return response()->json([
                    'message' => 'No FCM token found for user.',
                    'status' => false
                ], 400);
            }

            $result = $this->fcmService->sendToDevice(
                $user->fcm_token,
                $request->title,
                $request->body,
                [
                    'type' => 'test',
                    'timestamp' => now()->toISOString()
                ]
            );

            return response()->json([
                'message' => 'Test notification sent successfully.',
                'status' => true,
                'result' => $result
            ], 200);
        } catch (\Exception $e) {
            Log::error('Test notification failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send test notification.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FCM Token (Web Interface)
     *
     * Updates the FCM token for the authenticated user's device from web interface.
     */
    public function updateTokenWeb(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            $user = Auth::user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            // Validate the token with Firebase
            $isValid = $this->fcmService->validateToken($request->fcm_token);

            if (!$isValid) {
                Log::warning('Invalid FCM token provided', [
                    'user_id' => $user->id,
                    'token' => $request->fcm_token
                ]);
            }

            return response()->json([
                'message' => 'FCM token updated successfully.',
                'status' => true,
                'token_valid' => $isValid
            ], 200);
        } catch (\Exception $e) {
            Log::error('FCM token update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update FCM token.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove FCM Token (Web Interface)
     *
     * Removes the FCM token for the authenticated user from web interface.
     */
    public function removeTokenWeb(Request $request)
    {
        try {
            $user = Auth::user();
            $user->fcm_token = null;
            $user->save();

            return response()->json([
                'message' => 'FCM token removed successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('FCM token removal failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to remove FCM token.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
