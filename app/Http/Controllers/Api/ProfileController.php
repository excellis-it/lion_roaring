<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * @group Profile
 */
class ProfileController extends Controller
{
    protected $successStatus = 200;
    use ImageTrait;
    /**
     * Profile Details
     * @authenticated
     *
     * @response 200 {
     * "status": true,
     * "message": "Profile details",
     * "data": {
     *    "id": 2,
     *    "user_name": "john_doe",
     *    "first_name": "John",
     *    "middle_name": null,
     *    "last_name": "Doe",
     *    "email": "john@yopmail.com",
     *    "phone": "7415236986",
     *    "email_verified_at": null,
     *    "profile_picture": "profile_picture/1h5ihHDrrOf3Fp4O0Fg1EnLLkhuXn7vW4C1CAUZY.jpg",
     *    "address": "51 DN Block Merlin Infinite Building, 9th Floor, Unit, 907, Sector V, Bidhannagar, Kolkata, West Bengal 700091",
     *    "status": 1,
     *    "created_at": "2024-03-05T10:58:13.000000Z",
     *    "updated_at": "2024-04-18T12:27:38.000000Z"
     *    }
     * }
     */

    public function profile(Request $request)
    {
        $user = $request->user()->load('ecclesia', 'countries', 'states');
        return response()->json(['status' => true, 'message' => 'Profile details', 'data' => $user], $this->successStatus);
    }

    /**
     * Update Profile
     * @authenticated
     *
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam middle_name string optional The middle name of the user. Example: Doe
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam phone string required The phone number of the user. Example: 7415236986
     * @bodyParam address string required The address of the user. Example: 51 DN Block Merlin Infinite Building, 9th Floor, Unit, 907, Sector V, Bidhannagar, Kolkata, West Bengal 700091
     *
     * @response 200 {
     * "status": true,
     * "message": "Profile updated successfully"
     * }
     */

    public function updateProfile(Request $request)
    {
        $validator = validator($request->all(), [
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        $user = $request->user();
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Profile updated successfully'], $this->successStatus);
    }


    /**
     * Change Password
     * @authenticated
     *
     * @bodyParam old_password string required The old password of the user. Example: password
     * @bodyParam new_password string required The new password of the user. Example: password123
     * @bodyParam confirm_password string required The confirm password of the user. Example: password123
     *
     * @response 200 {
     * "status": true,
     * "message": "Password changed successfully"
     * }
     */

    public function changePassword(Request $request)
    {
        $validator = validator($request->all(), [
            'old_password' => 'required|min:8|password',
            'new_password' => 'required|min:8|different:old_password',
            'confirm_password' => 'required|min:8|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        $user = $request->user();
        if (password_verify($request->old_password, $user->password)) {
            $user->password = bcrypt($request->new_password);
            $user->save();
            return response()->json(['status' => true, 'message' => 'Password changed successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => 'Old password is incorrect'], 201);
        }
    }

    /**
     * Update Profile Picture
     * @authenticated
     * @bodyParam profile_picture file required The profile picture of the user.
     * @response 200 {
     * "status": true,
     * "message": "Profile picture updated successfully"
     * }
     * @response 201 {
     * "status": false,
     * "message": "The profile picture must be an image."
     * }
     */

    public function profilePictureUpdate(Request $request)
    {
        $validator = validator($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        $user = $request->user();
        $user->profile_picture = $this->imageUpload($request->file('profile_picture'), 'profile_picture');
        $user->save();

        return response()->json(['status' => true, 'message' => 'Profile picture updated successfully'], $this->successStatus);
    }


    /**
     * Check User Permission
     * 
     * Checks whether the authenticated user has a given permission.
     * @authenticated
     * @bodyParam permission_name string required The name of the permission to check. Example: Manage Email
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "User has permission"
     * }
     * @response 403 {
     *  "status": false,
     *  "message": "User does not have permission"
     * }
     * @response 422 {
     *  "message": "The permission_name field is required.",
     *  "status": false
     * }
     * @response 500 {
     *  "status": false,
     *  "message": "An error occurred: [error_message]"
     * }
     */
    public function checkUserHasPermission(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'permission_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => false
            ], 422);
        }

        try {
            // Check if the user has the requested permission
            if ($user->hasPermissionTo($request->input('permission_name'), 'web')) {
                return response()->json([
                    'status' => true,
                    'message' => 'User has permission'
                ], 200); // 200 OK
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User does not have permission'
                ], 403); // 403 Forbidden
            }
        } catch (\Exception $e) {
            // Handle any errors that occur during the permission check
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 201);
        }
    }


    /**
     * Check Menu Permissions
     * 
     * This endpoint checks the menu permissions for the authenticated user.
     *
     * @authenticated
     *
     * @response 200 {
     *   "status": true,
     *   "menus": [
     *     {
     *       "menu_name": "menu_a",
     *       "permission_name": "permission_name_aa",
     *       "active": true
     *     },
     *     {
     *       "menu_name": "menu_b",
     *       "permission_name": "permission_name_bb",
     *       "active": false
     *     },
     *     {
     *       "menu_name": "menu_c",
     *       "permission_name": "permission_name_cc",
     *       "active": true
     *     }
     *   ],
     *   "message": "Menu permissions fetched successfully"
     * }
     * 
     * @response 403 {
     *   "status": false,
     *   "message": "User does not have permission"
     * }
     * 
     * @response 201 {
     *   "status": false,
     *   "message": "An error occurred: <error_message>"
     * }
     */
    public function checkUserMenuPermission(Request $request)
    {
        $user = $request->user();
        // Define the menu-permission mapping dynamically
        $menus = [
            ['menu_name' => 'Chats', 'permission_name' => 'Manage Chat'],
            ['menu_name' => 'Team', 'permission_name' => 'Manage Team'],
            ['menu_name' => 'Mail', 'permission_name' => 'Manage Email'],
            ['menu_name' => 'Topics', 'permission_name' => 'Manage Topic'],
            ['menu_name' => 'Becoming Sovereign', 'permission_name' => 'Manage Becoming Sovereigns'],
            ['menu_name' => 'Becoming Christ Like', 'permission_name' => 'Manage Becoming Christ Like'],
            ['menu_name' => 'Becoming a Leader', 'permission_name' => 'Manage Becoming a Leader'],
            ['menu_name' => 'Files', 'permission_name' => 'Manage File'],
            ['menu_name' => 'Bulletin Board', 'permission_name' => 'Manage Bulletin'],
            ['menu_name' => 'Create Bulletin', 'permission_name' => 'Manage Bulletin'],
            ['menu_name' => 'Job Posting', 'permission_name' => 'Manage Job Postings'],
            ['menu_name' => 'Meeting Schedule', 'permission_name' => 'Manage Meeting Schedule'],
            ['menu_name' => 'Live Events', 'permission_name' => 'Manage Event'],
            ['menu_name' => 'All Members', 'permission_name' => 'Manage Partners'],
            ['menu_name' => 'Strategy', 'permission_name' => 'Manage Strategy'],
            ['menu_name' => 'Help', 'permission_name' => 'Manage Help'],
        ];

        try {
            $menuPermissions = [];

            foreach ($menus as $menu) {
                // $hasPermission = $user->hasPermissionTo($menu['permission_name'], 'web');
                try {
                    // Check if the user has the permission
                    $hasPermission = $user->hasPermissionTo($menu['permission_name'], 'web');
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    // If the permission does not exist, set active to false
                    $hasPermission = false;
                }

                $menuPermissions[] = [
                    'menu_name' => $menu['menu_name'],
                    'permission_name' => $menu['permission_name'],
                    'active' => $hasPermission, // Set active based on the permission status
                ];
            }

            // Return the list of menus with permission status
            return response()->json([
                'status' => true,
                'menus' => $menuPermissions,
                'message' => 'Menu permissions fetched successfully',
            ], 200); // 200 OK
        } catch (\Exception $e) {
            // Handle any errors that occur during the permission check
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 201); // 201 Created for error handling (you may want to use 500 for internal errors)
        }
    }
}
