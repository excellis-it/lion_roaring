<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\MailUser;
use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\TeamChat;
use Illuminate\Support\Facades\Crypt;

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
        $user = $request->user()->load([
            'ecclesia:id,name',
            'countries:id,name,code',
            'states:id,name,country_id',
            'userRole:id,name,type,is_ecclesia',
            'userLastSubscription',
        ]);
        $idParts = $this->resolveLionRoaringIdParts($user);

        return response()->json([
            'status' => true,
            'message' => 'Profile details',
            'in_app_membership' => (bool) config('lion_roaring.in_app_membership'),
            'data' => $this->formatProfilePayload($user, $idParts),
        ], $this->successStatus);
    }

    /**
     * Slim profile payload for mobile (avoids huge nested relation graphs).
     *
     * @param  array{prefix: string, suffix: string}  $idParts
     */
    private function formatProfilePayload(User $user, array $idParts): array
    {
        return [
            'id' => $user->id,
            'ecclesia_id' => $user->ecclesia_id,
            'user_name' => $user->user_name,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'phone_country_code_name' => $user->phone_country_code_name,
            'profile_picture' => $user->profile_picture,
            'address' => $user->address,
            'address2' => $user->address2,
            'city' => $user->city,
            'state' => $user->state,
            'country' => $user->country,
            'zip' => $user->zip,
            'status' => $user->status,
            'user_type' => $user->user_type,
            'lion_roaring_id' => $user->lion_roaring_id,
            'roar_id' => $user->roar_id,
            'generated_id_part' => $idParts['prefix'],
            'lion_roaring_id_suffix' => $idParts['suffix'],
            'time_zone' => $user->time_zone,
            'location_lat' => $user->location_lat,
            'location_lng' => $user->location_lng,
            'location_address' => $user->location_address,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'ecclesia' => $user->ecclesia,
            'countries' => $user->countries,
            'states' => $user->states,
            'user_role' => $user->userRole,
            'user_last_subscription' => $user->userLastSubscription,
        ];
    }

    /**
     * Update Profile
     * @authenticated
     *
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam middle_name string optional The middle name of the user. Example: Doe
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam country_code string optional The country code of the user. Example: 91
     * @bodyParam phone_number string required The phone number of the user. Example: 7415236986
     * @bodyParam address string required The address of the user. Example: 51 DN Block Merlin Infinite Building, 9th Floor, Unit, 907, Sector V, Bidhannagar, Kolkata, West Bengal 700091
     * @bodyParam country string required The country of the user. Example: India
     * @bodyParam state string required The state of the user. Example: West Bengal
     * @bodyParam city string required The city of the user. Example: Kolkata
     * @bodyParam zip string required The zip code of the user. Example: 700091
     *
     * @response 200 {
     * "status": true,
     * "message": "Profile updated successfully"
     * }
     */

    public function updateProfile(Request $request)
    {
        $validator = validator($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'lion_roaring_id_suffix' => 'required|digits:4',
            'generated_id_part' => 'required|string',
            'roar_id' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        $user = $request->user();
        $fullLionRoaringId = $request->generated_id_part . $request->lion_roaring_id_suffix;

        if (User::where('lion_roaring_id', $fullLionRoaringId)->where('id', '!=', $user->id)->exists()) {
            return response()->json([
                'message' => 'This Lion Roaring ID already exists.',
                'status' => false,
            ], 201);
        }

        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->lion_roaring_id = $fullLionRoaringId;
        $user->roar_id = $request->roar_id;
        $user->address = $request->address ?? '';
        $user->address2 = $request->address2 ?? '';
        $user->country = $request->country ?? '';
        $user->state = $request->state ?? '';
        $user->city = $request->city ?? '';
        $user->zip = $request->zip ?? '';

        $user->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone_number : $request->phone_number;
        $user->phone_country_code_name = $request->phone_country_code_name;
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
            'new_password' => ['required', 'different:old_password', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'required|min:8|same:new_password',
        ], [
            'old_password.password' => 'Old password is not correct',
            'new_password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&',
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
            ['menu_name' => 'Private Collaboration', 'permission_name' => 'Manage Private Collaboration'],

            ['menu_name' => 'All Members', 'permission_name' => 'Manage Partners'],
            ['menu_name' => 'Strategy', 'permission_name' => 'Manage Strategy'],
            ['menu_name' => 'Help', 'permission_name' => 'Manage Help'],
            ['menu_name' => 'Policy', 'permission_name' => 'Manage Policy'],

            ['menu_name' => 'Estore CMS', 'permission_name' => 'Manage Estore CMS'],
            ['menu_name' => 'Estore Users', 'permission_name' => 'Manage Estore Users'],
            ['menu_name' => 'Estore Category', 'permission_name' => 'Manage Estore Category'],
            ['menu_name' => 'Estore Sizes', 'permission_name' => 'Manage Estore Sizes'],
            ['menu_name' => 'Estore Colors', 'permission_name' => 'Manage Estore Colors'],
            ['menu_name' => 'Estore Products', 'permission_name' => 'Manage Estore Products'],
            ['menu_name' => 'Estore Settings', 'permission_name' => 'Manage Estore Settings'],
            ['menu_name' => 'Estore Warehouse', 'permission_name' => 'Manage Estore Warehouse'],
            ['menu_name' => 'Estore Orders', 'permission_name' => 'Manage Estore Orders'],
            ['menu_name' => 'Order Status', 'permission_name' => 'Manage Order Status'],
            ['menu_name' => 'Email Template', 'permission_name' => 'Manage Email Template'],
            ['menu_name' => 'Elearning CMS', 'permission_name' => 'Manage Elearning CMS'],

            ['menu_name' => 'Elearning Category', 'permission_name' => 'Manage Elearning Category'],
            ['menu_name' => 'Elearning Product', 'permission_name' => 'Manage Elearning Product'],
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

    /**
     * Notifications list
     *
     * @authenticated
     *
     *
     * @response 200
     *   {
     *    "list": {
     *        "current_page": 1,
     *        "data": [
     *            {
     *                "id": 862,
     *                "user_id": 37,
     *                "chat_id": null,
     *                "message": "You have a <b>new mail</b> from masum@excellisit.net",
     *                "status": 0,
     *                "type": "Mail",
     *                "is_read": 0,
     *                "is_delete": 0,
     *                "created_at": "2024-12-04T05:40:32.000000Z",
     *                "updated_at": "2024-12-04T05:40:32.000000Z"
     *            },
     *            {
     *                "id": 860,
     *                "user_id": 37,
     *                "chat_id": null,
     *                "message": "You have a <b>new mail</b> from masum@excellisit.net",
     *                "status": 0,
     *                "type": "Mail",
     *                "is_read": 0,
     *                "is_delete": 0,
     *                "created_at": "2024-12-04T05:37:44.000000Z",
     *                "updated_at": "2024-12-04T05:37:44.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http://127.0.0.1:8000/api/v3/user/notifications?page=1",
     *        "from": 1,
     *        "last_page": 15,
     *        "last_page_url": "http://127.0.0.1:8000/api/v3/user/notifications?page=15",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http://127.0.0.1:8000/api/v3/user/notifications?page=1",
     *                "label": "1",
     *                "active": true
     *            },
     *            {
     *                "url": "http://127.0.0.1:8000/api/v3/user/notifications?page=2",
     *                "label": "2",
     *                "active": false
     *            },
     *            {
     *                "url": "http://127.0.0.1:8000/api/v3/user/notifications?page=2",
     *                "label": "Next &raquo;",
     *                "active": false
     *            }
     *        ],
     *        "next_page_url": "http://127.0.0.1:8000/api/v3/user/notifications?page=2",
     *        "path": "http://127.0.0.1:8000/api/v3/user/notifications",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 15,
     *        "total": 216
     *    }
     * }
     * @response 201 {
     *   "message": "Page not found"
     * }
     */
    public function notifications(Request $request)
    {
        try {


            $notifications = Notification::where('user_id', auth()->user()->id)->where('is_delete', 0)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $is_notification = true;

            return response()->json([
                'list' => $notifications
            ], 200);


            return response()->json(['message' => 'Page not found'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 201);
        }
    }

    /**
     * Mark a notification as read.
     *
     * @authenticated
     *
     * @urlParam type string The type of notification (Chat, Team, Mail). Example: Chat
     * @urlParam id int The ID of the notification. Example: 1
     *
     * @response 200 {
     *   "message": "Notification marked as read"
     * }
     * @response 404 {
     *   "message": "Notification not found"
     * }
     */
    public function notificationRead($type, $id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json(['message' => 'Notification not found'], 404);
            }

            $pagename = 'no';

            if ($type == 'Chat') {
                $pagename = 'chat';
            } elseif ($type == 'Team') {
                $pagename = 'team';
            } elseif ($type == 'Mail') {
                $pagename = 'mail';
            }

            $notification->is_read = 1;
            $notification->update();

            return response()->json(['page_name' => $pagename, 'message' => 'Notification marked as read'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 201);
        }
    }



    /**
     * Delete all notifications
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Notification deleted successfully.",
     *   "status": true
     * }
     */
    public function notificationClear()
    {
        //  return response()->json(['message' => 'hello'], 200);
        try {
            Notification::where('user_id', auth()->user()->id)->delete();
            return response()->json(['message' => 'Notification deleted successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 201);
        }
    }

    // updateFcmToken
    /**
     * Update FCM Token
     *
     * @authenticated
     *
     * @bodyParam fcm_token string required The FCM token of the user. Example: fcm_token_example
     *
     * @response 200 {
     *   "status": true,
     *   "message": "FCM token updated successfully"
     * }
     */
    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['status' => true, 'message' => 'FCM token updated successfully'], $this->successStatus);
    }

    // // Get total unread messages count mail,chat,team-chat unreadMessagesCount
    /**
     * Get Total Unread Messages Count
     *
     * @authenticated
     *
     * @response 200 {
     *   "status": true,
     *   "data": {
     *       "mail": 5,
     *       "chat": 3,
     *       "team_chat": 2,
     *       "total": 10
     *   }
     * }
     */
    public function unreadMessagesCount(Request $request)
    {
        $user = $request->user();

        // Count unread mails where user is receiver and not deleted
        $mailCount = MailUser::where('user_id', $user->id)
            ->where('is_to', 1)      // Only count mails where user is receiver
            ->where('is_delete', 0)  // Not deleted
            ->where('is_read', 0)    // Not read
            ->count();

        // Count unread individual chats where user is receiver and not deleted
        // AND sender is an active user with valid role (matching chat list display logic)
        $chatCount = Chat::where('reciver_id', $user->id)
            ->where('seen', 0)
            ->where('deleted_for_reciver', 0)
            ->where('delete_from_receiver_id', 0)
            ->whereHas('sender', function ($query) {
                // Only count messages from active users with valid roles
                $query->where('status', 1)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('type', [1, 2, 3]);
                    });
            })
            ->count();

        // Count unread team chat messages where:
        // 1. User is a member and not removed from team
        // 2. Message is not seen by user
        // 3. Message is not soft deleted
        $teamChatCount = ChatMember::where('user_id', $user->id)
            ->where('is_seen', 0)
            ->whereHas('chat', function ($query) {
                // Only count messages that are not soft deleted
                $query->whereNull('deleted_at');
            })
            ->whereHas('chat.team.members', function ($query) use ($user) {
                // Check user is still a member of the team and not removed
                $query->where('user_id', $user->id)
                    ->where('is_removed', false);
            })
            ->count();

        $totalCount = $mailCount + $chatCount + $teamChatCount;

        return response()->json([
            'status' => true,
            'data' => [
                'mail' => $mailCount,
                'chat' => $chatCount,
                'team_chat' => $teamChatCount,
                'total' => $totalCount,
            ]
        ], $this->successStatus);
    }

    /**
     * In-App Notification Count
     *
     * Returns the total count of unread, non-deleted in-app notifications.
     * Mirrors Helper::notificationCount() — matches the web header bell number.
     *
     * @authenticated
     *
     * @response 200 {
     *   "status": true,
     *   "data": { "total": 885 }
     * }
     */
    public function inAppNotificationCount(Request $request)
    {
        $user = $request->user();

        // Non-chat notifications: all unread, non-deleted
        $baseCount = Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->where('is_delete', 0)
            ->where('type', '!=', 'Chat')
            ->count();

        // Chat notifications: unread, non-deleted, filtered by valid sender
        $chatCount = Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->where('is_delete', 0)
            ->where('type', 'Chat')
            ->whereHas('chat.sender', function ($query) use ($user) {
                $query->where('status', 1)
                    ->whereHas('userRole', function ($q) {
                        $q->whereIn('type', [1, 2, 3]);
                    });

                if (!$user->hasNewRole('SUPER ADMIN')) {
                    $userType    = $user->user_type;
                    $countryName = $user->country;
                    $authId      = $user->id;

                    $query->where(function ($q) use ($userType, $countryName, $authId) {
                        if ($userType === 'Global') {
                            $q->where(function ($sq) {
                                $sq->where('user_type', 'Global')
                                    ->whereDoesntHave('userRole', fn ($r) => $r->where('name', 'SUPER ADMIN'));
                            })->orWhere(function ($sq) use ($authId) {
                                $sq->whereHas('userRole', fn ($r) => $r->where('name', 'SUPER ADMIN'))
                                    ->whereHas('chatSender', fn ($c) => $c->where('reciver_id', $authId));
                            });
                        } else {
                            $q->where('country', $countryName)
                                ->whereDoesntHave('userRole', fn ($r) => $r->where('name', 'SUPER ADMIN'))
                                ->orWhere(function ($sq) use ($authId) {
                                    $sq->whereHas('userRole', fn ($r) => $r->where('name', 'SUPER ADMIN'))
                                        ->whereHas('chatSender', fn ($c) => $c->where('reciver_id', $authId));
                                });
                        }
                    });
                }
            })
            ->count();

        return response()->json([
            'status' => true,
            'data'   => ['total' => $baseCount + $chatCount],
        ], 200);
    }

    /**
     * Build Lion Roaring ID prefix/suffix for profile display and editing (matches web user profile).
     *
     * @return array{prefix: string, suffix: string}
     */
    private function resolveLionRoaringIdParts(User $user): array
    {
        $todayCount = User::withTrashed()->whereDate('created_at', now()->toDateString())->count();
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $datePart = now()->format('mdY');
        $generatedPrefix = 'LR' . $sequence . $datePart;

        $existingId = (string) ($user->lion_roaring_id ?? '');
        $currentPrefix = $generatedPrefix;
        $currentSuffix = '';

        if (strlen($existingId) >= 14 && substr($existingId, 0, 2) === 'LR') {
            $currentPrefix = substr($existingId, 0, 14);
            $currentSuffix = substr($existingId, 14);
        } elseif ($existingId !== '') {
            $currentSuffix = $existingId;
        }

        return [
            'prefix' => $currentPrefix,
            'suffix' => $currentSuffix,
        ];
    }
}
