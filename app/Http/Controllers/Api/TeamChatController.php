<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\ChatMember;
use App\Models\Notification;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamMember;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Group Chats
 */

class TeamChatController extends Controller
{

    use ImageTrait;

    public function userLastMessage($team_id, $user_id)
    {
        return TeamChat::where('team_id', $team_id)->whereHas('chatMembers', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->latest()->first();
    }

    /**
     * List of Group Chats
     * 
     * Retrieves the list of teams that the authenticated user is part of, ordered by the last message sent in the team. It also includes the team members.
     * @authenticated
     * 
     * @response 200 *{
     *  "msg": "Teams listed successfully",
     *  "status": true,
     *  "teams": [
     *    {
     *      "id": 31,
     *      "created_by": null,
     *      "name": "test group",
     *      "group_image": "team/mzw14lBFdUuQKRMiOSjEoZOKPIFvxXPYyJxl3CSq.png",
     *      "description": "for testing",
     *      "created_at": "2024-11-05T11:10:08.000000Z",
     *      "updated_at": "2024-11-05T11:10:08.000000Z",
     *      "chats": [
     *        {
     *          "id": 279,
     *          "team_id": 31,
     *          "user_id": 37,
     *          "message": "Welcome to test group group.",
     *          "attachment": null,
     *          "is_seen": 0,
     *          "deleted_at": null,
     *          "created_at": "2024-11-05T11:10:08.000000Z",
     *          "updated_at": "2024-11-05T11:10:08.000000Z",
     *          "chat_members": [
     *            {
     *              "id": 919,
     *              "chat_id": 279,
     *              "user_id": 38,
     *              "is_seen": 0,
     *              "created_at": "2024-11-05T11:10:08.000000Z",
     *              "updated_at": "2024-11-05T11:10:08.000000Z"
     *            },
     *            {
     *              "id": 920,
     *              "chat_id": 279,
     *              "user_id": 12,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:08.000000Z",
     *              "updated_at": "2024-11-05T11:10:53.000000Z"
     *            },
     *            {
     *              "id": 921,
     *              "chat_id": 279,
     *              "user_id": 37,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:08.000000Z",
     *              "updated_at": "2024-11-05T11:10:46.000000Z"
     *            }
     *          ]
     *        },
     *        {
     *          "id": 280,
     *          "team_id": 31,
     *          "user_id": 37,
     *          "message": "a",
     *          "attachment": null,
     *          "is_seen": 0,
     *          "deleted_at": null,
     *          "created_at": "2024-11-05T11:10:46.000000Z",
     *          "updated_at": "2024-11-05T11:10:46.000000Z",
     *          "chat_members": [
     *            {
     *              "id": 922,
     *              "chat_id": 280,
     *              "user_id": 37,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:46.000000Z",
     *              "updated_at": "2024-11-05T11:10:46.000000Z"
     *            },
     *            {
     *              "id": 923,
     *              "chat_id": 280,
     *              "user_id": 38,
     *              "is_seen": 0,
     *              "created_at": "2024-11-05T11:10:46.000000Z",
     *              "updated_at": "2024-11-05T11:10:46.000000Z"
     *            },
     *            {
     *              "id": 924,
     *              "chat_id": 280,
     *              "user_id": 12,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:46.000000Z",
     *              "updated_at": "2024-11-05T11:10:53.000000Z"
     *            }
     *          ]
     *        },
     *        {
     *          "id": 281,
     *          "team_id": 31,
     *          "user_id": 12,
     *          "message": "b",
     *          "attachment": null,
     *          "is_seen": 0,
     *          "deleted_at": null,
     *          "created_at": "2024-11-05T11:10:53.000000Z",
     *          "updated_at": "2024-11-05T11:10:53.000000Z",
     *          "chat_members": [
     *            {
     *              "id": 925,
     *              "chat_id": 281,
     *              "user_id": 37,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:53.000000Z",
     *              "updated_at": "2024-11-05T11:10:54.000000Z"
     *            },
     *            {
     *              "id": 926,
     *              "chat_id": 281,
     *              "user_id": 38,
     *              "is_seen": 0,
     *              "created_at": "2024-11-05T11:10:53.000000Z",
     *              "updated_at": "2024-11-05T11:10:53.000000Z"
     *            },
     *            {
     *              "id": 927,
     *              "chat_id": 281,
     *              "user_id": 12,
     *              "is_seen": 1,
     *              "created_at": "2024-11-05T11:10:53.000000Z",
     *              "updated_at": "2024-11-05T11:10:53.000000Z"
     *            }
     *          ]
     *        }
     *      ],
     *      "last_message": {
     *        "id": 281,
     *        "team_id": 31,
     *        "user_id": 12,
     *        "message": "b",
     *        "attachment": null,
     *        "is_seen": 0,
     *        "deleted_at": null,
     *        "created_at": "2024-11-05T11:10:53.000000Z",
     *        "updated_at": "2024-11-05T11:10:53.000000Z"
     *      }
     *    }
     *  ],
     *  "members": [
     *    {
     *      "id": 1,
     *      "ecclesia_id": null,
     *      "created_id": "0",
     *      "user_name": "user1",
     *      "first_name": "abc",
     *      "middle_name": null,
     *      "last_name": "def",
     *      "email": "main@test.com",
     *      "phone": "+36 12345",
     *      "email_verified_at": null,
     *      "profile_picture": "profile/5GI3lBuRJl6QOxp55xLpNEWwxIuedycBSF6WAT6H.png",
     *      "address": "Kokata",
     *      "city": "Kolkata",
     *      "state": "41",
     *      "address2": null,
     *      "country": "101",
     *      "zip": "700001",
     *      "status": 1,
     *      "created_at": "2024-03-05T15:36:13.000000Z",
     *      "updated_at": "2024-09-04T11:09:09.000000Z"
     *    },
     *    {
     *      "id": 19,
     *      "ecclesia_id": null,
     *      "created_id": "1",
     *      "user_name": "Allen",
     *      "first_name": "Allen",
     *      "middle_name": null,
     *      "last_name": "Allen",
     *      "email": "allen@yopmail.com",
     *      "phone": "516-313-5564",
     *      "email_verified_at": null,
     *      "profile_picture": null,
     *      "address": "santa Ana",
     *      "city": "Orange",
     *      "state": "California",
     *      "address2": null,
     *      "country": "United States",
     *      "zip": "915467",
     *      "status": 1,
     *      "created_at": "2024-07-27T07:16:03.000000Z",
     *      "updated_at": "2024-07-27T07:16:03.000000Z"
     *    },
     *  ]
     *}
     */
    public function list(Request $request)
    {
        try {
            // Get the teams that the authenticated user is a member of
            $teams = Team::with('chats.chatMembers')->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('id', 'desc')->get()->toArray();

            // Get the last message sent in each team
            $teams = array_map(function ($team) {
                $team['last_message'] = $this->userLastMessage($team['id'], auth()->id());
                return $team;
            }, $teams);

            // Sort teams based on the latest message
            usort($teams, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move teams with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move teams with no messages to the end
                }
                return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            });

            // Get members who are not the authenticated user
            $members = User::orderBy('first_name', 'asc')->where('id', '!=', auth()->id())->where('status', true)->get();

            return response()->json([
                'msg' => 'Teams listed successfully',
                'status' => true,
                'teams' => $teams,
                'members' => $members
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'status' => false
            ], 201);
        }
    }



    /**
     * Create a New Group
     * 
     * Creates a new group with the specified members and a welcome message. The authenticated user will be set as the group admin.
     * @authenticated
     * 
     * @bodyParam name string required The name of the team. Example: "Project Z Team"
     * @bodyParam description string required A brief description of the team. Example: "Team for Project Z collaboration"
     * @bodyParam members int[] required The IDs of the users to be added to the group. Example: members[]=1 & members[]=2
     * @bodyParam group_image file required An image file for the team group. Supported formats: jpeg, png, jpg, gif, svg. Maximum size: 2MB.
     * 
     * @response 200 *{
     *    "message": "Team created successfully.",
     *    "status": true,
     *    "team": {
     *        "id": 33,
     *        "created_by": null,
     *        "name": "abc",
     *        "group_image": "team/hEjqR3bR46pdpRncBqugbo9aZNC7gFVkNNi9cMzG.png",
     *        "description": "test 2",
     *        "created_at": "2024-11-07T10:32:47.000000Z",
     *        "updated_at": "2024-11-07T10:32:47.000000Z",
     *        "last_message": {
     *            "id": 283,
     *            "team_id": 33,
     *            "user_id": 37,
     *            "message": "Welcome to abc group.",
     *            "attachment": null,
     *            "is_seen": 0,
     *            "deleted_at": null,
     *            "created_at": "2024-11-07T10:32:47.000000Z",
     *            "updated_at": "2024-11-07T10:32:47.000000Z"
     *        }
     *    },
     *    "chat_member_id": [
     *        12,
     *        38,
     *        37
     *    ]
     * }
     */
    public function create(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'name' => 'required|max:100',
                'description' => 'required|max:255',
                'members' => 'required|array|min:1',
                'members.*' => 'required|exists:users,id',
                'group_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Create the new team
            $team = new Team();
            $team->name = $request->name;
            $team->description = $request->description;
            $team->group_image = $this->imageUpload($request->file('group_image'), 'team');
            $team->save();

            // Add the authenticated user as the team admin
            $admin_member = new TeamMember();
            $admin_member->team_id = $team->id;
            $admin_member->user_id = auth()->id();
            $admin_member->is_admin = true;
            $admin_member->save();

            // Add other members to the team
            foreach ($request->members as $member_id) {
                $team_member = new TeamMember();
                $team_member->team_id = $team->id;
                $team_member->user_id = $member_id;
                $team_member->is_admin = false;
                $team_member->save();
            }

            // Create a team chat and a welcome message
            $team_chat = new TeamChat();
            $team_chat->team_id = $team->id;
            $team_chat->user_id = auth()->id();
            $team_chat->message = 'Welcome to ' . $team->name . ' group.';
            $team_chat->save();

            // Add chat members and notifications
            foreach ($request->members as $member_id) {
                // Add each member to the chat
                $chat_member = new ChatMember();
                $chat_member->chat_id = $team_chat->id;
                $chat_member->user_id = $member_id;
                $chat_member->save();

                // Create a notification for each member
                $notification = new Notification();
                $notification->user_id = $member_id;
                $notification->message = 'You have been added to <b>' . $team->name . '</b> group.';
                $notification->type = 'Team';
                $notification->save();
            }

            // Add the admin to the chat as well
            $admin_chat_member = new ChatMember();
            $admin_chat_member->chat_id = $team_chat->id;
            $admin_chat_member->user_id = auth()->id();
            $admin_chat_member->save();

            // Fetch the newly created team with the last message
            $team = Team::with('lastMessage')->find($team->id);

            // Get the IDs of the chat members
            $chat_member_id = ChatMember::where('chat_id', $team_chat->id)->pluck('user_id')->toArray();

            // Return a JSON response with the team and chat member IDs
            return response()->json([
                'message' => 'Team created successfully.',
                'status' => true,
                'team' => $team,
                'chat_member_id' => $chat_member_id
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => false
            ], 201);
        }
    }


    /**
     * Load Specific Group Chats
     *
     * Retrieves a team's information and chat history for the authenticated user, along with member details.
     * @authenticated
     * @bodyParam team_id int required The ID of the team to load chats from. Example: 5
     * 
     * @response 200 {
     *    "team": {
     *        "id": 32,
     *        "created_by": null,
     *        "name": "abc 1",
     *        "group_image": "team\/S7oRcO09C1lQad6zaPE8JgU1vrwnRY7fhiMQLCpj.png",
     *        "description": "test 1",
     *        "created_at": "2024-11-07T10:16:41.000000Z",
     *        "updated_at": "2024-11-07T10:16:41.000000Z",
     *        "members": [
     *            {
     *                "id": 118,
     *                "team_id": 32,
     *                "user_id": 37,
     *                "is_removed": 0,
     *                "is_admin": 1,
     *                "is_removed_at": null,
     *                "created_at": "2024-11-07T10:16:41.000000Z",
     *                "updated_at": "2024-11-07T10:16:41.000000Z",
     *                "user": {
     *                    "id": 37,
     *                    "ecclesia_id": 4,
     *                    "created_id": null,
     *                    "user_name": "masum1",
     *                    "first_name": "masum",
     *                    "middle_name": null,
     *                    "last_name": "ali",
     *                    "email": "masum@excellisit.net",
     *                    "phone": "+91 9123456789",
     *                    "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                    "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                    "address": "kolkata",
     *                    "city": "kolkata",
     *                    "state": "41",
     *                    "address2": "kolkata",
     *                    "country": "101",
     *                    "zip": "700001",
     *                    "status": 1,
     *                    "created_at": "2024-10-28T08:35:17.000000Z",
     *                    "updated_at": "2024-11-06T07:42:16.000000Z"
     *                }
     *            },
     *            {
     *                "id": 119,
     *                "team_id": 32,
     *                "user_id": 38,
     *                "is_removed": 0,
     *                "is_admin": 0,
     *                "is_removed_at": null,
     *                "created_at": "2024-11-07T10:16:41.000000Z",
     *                "updated_at": "2024-11-07T10:16:41.000000Z",
     *                "user": {
     *                    "id": 38,
     *                    "ecclesia_id": 4,
     *                    "created_id": null,
     *                    "user_name": "masum2",
     *                    "first_name": "Masum",
     *                    "middle_name": null,
     *                    "last_name": "2",
     *                    "email": "masum2@excellisit.net",
     *                    "phone": "+91 11 1111 1111",
     *                    "email_verified_at": "2024-11-05T07:17:07.000000Z",
     *                    "profile_picture": null,
     *                    "address": "Kolkata",
     *                    "city": "Kolkata",
     *                    "state": "41",
     *                    "address2": null,
     *                    "country": "101",
     *                    "zip": "700001",
     *                    "status": 1,
     *                    "created_at": "2024-11-05T07:17:07.000000Z",
     *                    "updated_at": "2024-11-05T07:17:07.000000Z"
     *                }
     *            },
     *            {
     *                "id": 120,
     *                "team_id": 32,
     *                "user_id": 12,
     *                "is_removed": 0,
     *                "is_admin": 0,
     *                "is_removed_at": null,
     *                "created_at": "2024-11-07T10:16:41.000000Z",
     *                "updated_at": "2024-11-07T10:16:41.000000Z",
     *                "user": {
     *                    "id": 12,
     *                    "ecclesia_id": 2,
     *                    "created_id": "1",
     *                    "user_name": "swarnadwip_nath",
     *                    "first_name": "Swarnadwip",
     *                    "middle_name": null,
     *                    "last_name": "Nath",
     *                    "email": "swarnadwip@excellisit.net",
     *                    "phone": "+1 0741202022",
     *                    "email_verified_at": null,
     *                    "profile_picture": "profile_picture\/yCvplMhdpjc0kIeKG63tfkZwhKNYbcF1ZhfQdDFO.jpg",
     *                    "address": "Kokata",
     *                    "city": "Kolkata",
     *                    "state": "41",
     *                    "address2": null,
     *                    "country": "101",
     *                    "zip": "700001",
     *                    "status": 1,
     *                    "created_at": "2024-06-21T11:31:27.000000Z",
     *                    "updated_at": "2024-09-09T11:02:59.000000Z"
     *                }
     *            }
     *        ]
     *    },
     *    "team_chats": [
     *        {
     *            "id": 282,
     *            "team_id": 32,
     *            "user_id": 37,
     *            "message": "Welcome to abc 1 group.",
     *            "attachment": null,
     *            "is_seen": 0,
     *            "deleted_at": null,
     *            "created_at": "2024-11-07T10:16:41.000000Z",
     *            "updated_at": "2024-11-07T10:16:41.000000Z",
     *            "user": {
     *                "id": 37,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum1",
     *                "first_name": "masum",
     *                "middle_name": null,
     *                "last_name": "ali",
     *                "email": "masum@excellisit.net",
     *                "phone": "+91 9123456789",
     *                "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                "address": "kolkata",
     *                "city": "kolkata",
     *                "state": "41",
     *                "address2": "kolkata",
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-10-28T08:35:17.000000Z",
     *                "updated_at": "2024-11-06T07:42:16.000000Z"
     *            }
     *        },
     *        {
     *            "id": 284,
     *            "team_id": 32,
     *            "user_id": 37,
     *            "message": "good",
     *            "attachment": null,
     *            "is_seen": 0,
     *            "deleted_at": null,
     *            "created_at": "2024-11-07T10:51:54.000000Z",
     *            "updated_at": "2024-11-07T10:51:54.000000Z",
     *            "user": {
     *                "id": 37,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum1",
     *                "first_name": "masum",
     *                "middle_name": null,
     *                "last_name": "ali",
     *                "email": "masum@excellisit.net",
     *                "phone": "+91 9123456789",
     *                "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                "address": "kolkata",
     *                "city": "kolkata",
     *                "state": "41",
     *                "address2": "kolkata",
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-10-28T08:35:17.000000Z",
     *                "updated_at": "2024-11-06T07:42:16.000000Z"
     *            }
     *        }
     *    ],
     *    "team_member_names": "Test User, Masum  2, Swarnadwip  Nath"
     *}
     * @response 201 {
     *   "msg": "An error occurred while loading chats.",
     *   "status": false
     * }
     */
    public function load(Request $request)
    {
        try {
            $team_id = $request->team_id;

            // Get team information with members
            $team = Team::where('id', $team_id)
                ->with(['members', 'members.user'])
                ->first();

            // Get team chat messages
            $team_chats = TeamChat::where('team_id', $team_id)
                ->whereHas('chatMembers', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark chat as seen for the authenticated user
            ChatMember::where('user_id', auth()->id())
                ->whereHas('chat', function ($query) use ($team_id) {
                    $query->where('team_id', $team_id);
                })
                ->update(['is_seen' => true]);

            // Get comma-separated team member names
            $team_members = TeamMember::where('team_id', $team_id)
                ->where('is_removed', false)
                ->with('user')
                ->get();

            $team_member_name = '';
            foreach ($team_members as $member) {
                $team_member_name .= ($member->user->first_name ?? '') . ' ' .
                    ($member->user->middle_name ?? '') . ' ' .
                    ($member->user->last_name ?? '') . ', ';
            }
            $team_member_name = rtrim($team_member_name, ', ');

            // Respond with JSON data
            return response()->json([
                'team' => $team,
                'team_chats' => $team_chats,
                'team_member_names' => $team_member_name,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'status' => false], 201);
        }
    }


    /**
     * Send Group Chat Message
     *
     * Sends a message or attachment to a group chat for the authenticated user. If a file is provided, it will be uploaded as an attachment; otherwise, the message text will be sent.
     * @authenticated
     * @bodyParam team_id int required The ID of the team to send the message to. Example: 5
     * @bodyParam message string The text message to send (optional if a file is provided). Example: "Hello team!"
     * @bodyParam file file The attachment file to send, if applicable (optional if a message is provided).
     * 
     * @response {
     *    "message": "Message sent successfully.",
     *    "status": true,
     *    "chat": {
     *        "id": 289,
     *        "team_id": 32,
     *        "user_id": 37,
     *        "message": "hello teams",
     *        "attachment": null,
     *        "is_seen": 0,
     *        "deleted_at": null,
     *        "created_at": "2024-11-07T11:23:20.000000Z",
     *        "updated_at": "2024-11-07T11:23:20.000000Z",
     *        "user": {
     *            "id": 37,
     *            "ecclesia_id": 4,
     *            "created_id": null,
     *            "user_name": "masum1",
     *            "first_name": "Test",
     *            "middle_name": null,
     *            "last_name": "User",
     *            "email": "masum@excellisit.net",
     *            "phone": "+91 9123456789",
     *            "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *            "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *            "address": "kolkata",
     *            "city": "kolkata",
     *            "state": "41",
     *            "address2": "kolkata",
     *            "country": "101",
     *            "zip": "700001",
     *            "status": 1,
     *            "created_at": "2024-10-28T08:35:17.000000Z",
     *            "updated_at": "2024-11-06T07:42:16.000000Z"
     *        },
     *        "chat_members": [
     *            {
     *                "id": 949,
     *                "chat_id": 289,
     *                "user_id": 37,
     *                "is_seen": 1,
     *                "created_at": "2024-11-07T11:23:20.000000Z",
     *                "updated_at": "2024-11-07T11:23:20.000000Z"
     *            },
     *            {
     *                "id": 950,
     *                "chat_id": 289,
     *                "user_id": 38,
     *                "is_seen": 0,
     *                "created_at": "2024-11-07T11:23:20.000000Z",
     *                "updated_at": "2024-11-07T11:23:20.000000Z"
     *            },
     *            {
     *                "id": 951,
     *                "chat_id": 289,
     *                "user_id": 12,
     *                "is_seen": 0,
     *                "created_at": "2024-11-07T11:23:20.000000Z",
     *                "updated_at": "2024-11-07T11:23:20.000000Z"
     *            }
     *        ]
     *    },
     *    "chat_member_id": [
     *        37,
     *        38,
     *        12
     *    ]
     *}
     */
    public function send(Request $request)
    {
        try {
            // Create new chat entry
            $team_chat = new TeamChat();
            $team_chat->team_id = $request->team_id;
            $team_chat->user_id = auth()->id();

            // Handle file or message content
            if ($request->file) {
                $team_chat->attachment = $this->imageUpload($request->file('file'), 'team-chat');
            } else {
                $team_chat->message = $request->message;
            }
            $team_chat->save();

            // Retrieve team members and team data
            $team_members = TeamMember::where('team_id', $request->team_id)
                ->where('is_removed', false)
                ->get();

            foreach ($team_members as $team_member) {
                $chat_member = new ChatMember();
                $chat_member->chat_id = $team_chat->id;
                $chat_member->user_id = $team_member->user_id;
                $chat_member->is_seen = $team_member->user_id == auth()->id();
                $chat_member->save();

                // Create notifications for other members
                if ($team_member->user_id != auth()->id()) {
                    $notification = new Notification();
                    $notification->user_id = $team_member->user_id;
                    $notification->chat_id = $team_chat->id;
                    $notification->message = 'You have a new message in <b>' . Team::find($request->team_id)->name . '</b> group.';
                    $notification->type = 'Team';
                    $notification->save();
                }
            }

            // Get chat member IDs and chat details
            $chat_member_id = ChatMember::where('chat_id', $team_chat->id)->pluck('user_id')->toArray();
            $chat = TeamChat::where('id', $team_chat->id)->with('user', 'chatMembers')->first();

            // JSON response with chat and member IDs
            return response()->json([
                'message' => 'Message sent successfully.',
                'status' => true,
                'chat' => $chat,
                'chat_member_id' => $chat_member_id
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }


    /**
     * Get Group Information
     *
     * Retrieves detailed information about a team, including its active members and other available users.
     * @authenticated
     * @bodyParam team_id int required The ID of the team to retrieve information for. Example: 5
     *
     * @response {
     *    "message": "Group information retrieved successfully.",
     *    "status": true,
     *    "team": {
     *        "id": 5,
     *        "name": "Project Alpha",
     *        "description": "Development team for Project Alpha",
     *        "members": [
     *            {
     *                "user_id": 1,
     *                "first_name": "John",
     *                "last_name": "Doe",
     *                "email": "john@example.com"
     *            },
     *            {
     *                "user_id": 2,
     *                "first_name": "Jane",
     *                "last_name": "Smith",
     *                "email": "jane@example.com"
     *            }
     *        ]
     *    },
     *    "available_members": [
     *        {
     *            "id": 3,
     *            "first_name": "Mike",
     *            "last_name": "Taylor"
     *        },
     *        {
     *            "id": 4,
     *            "first_name": "Alice",
     *            "last_name": "Brown"
     *        }
     *    ]
     * }
     */
    public function groupInfo(Request $request)
    {
        try {
            $team_id = $request->team_id;

            // Retrieve the team and its active members
            $team = Team::where('id', $team_id)
                ->with(['members' => function ($query) {
                    $query->where('is_removed', false); // Include only active members
                }, 'members.user'])
                ->first();

            // If team not found, return error response
            if (!$team) {
                return response()->json(['message' => 'Team not found', 'status' => false], 201);
            }

            // Prepare team data with member details
            $team_data = [
                'id' => $team->id,
                'name' => $team->name,
                'description' => $team->description,
                'members' => $team->members->map(function ($member) {
                    return [
                        'user_id' => $member->user->id,
                        'first_name' => $member->user->first_name,
                        'last_name' => $member->user->last_name,
                        'email' => $member->user->email,
                    ];
                })
            ];

            // Retrieve additional members not in the team
            $available_members = User::orderBy('first_name', 'asc')
                ->where('id', '!=', auth()->id())
                ->where('status', true)
                ->get(['id', 'first_name', 'last_name']);

            return response()->json([
                'message' => 'Group information retrieved successfully.',
                'status' => true,
                'team' => $team_data,
                'available_members' => $available_members
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 201);
        }
    }


    /**
     * Update Group Image
     *
     * Updates the group image for a specific team.
     * @authenticated
     * @bodyParam team_id int required The ID of the team whose image is being updated. Example: 5
     * @bodyParam group_image file required The new group image file (JPEG, PNG, JPG, GIF, SVG formats, max size: 2048KB).
     *
     * @response 200 {
     *    "message": "Group image updated successfully.",
     *    "status": true,
     *    "group_image": "team/image.jpg"
     * }
     * @response 201 {
     *    "message": "An error occurred while updating the group image.",
     *    "status": false
     * }
     */
    public function updateGroupImage(Request $request)
    {
        try {
            $request->validate([
                'group_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $team = Team::find($request->team_id);

            // Check if team exists
            if (!$team) {
                return response()->json(['message' => 'Team not found', 'status' => false], 201);
            }

            // Update group image
            $team->group_image = $this->imageUpload($request->file('group_image'), 'team');
            $team->save();

            return response()->json([
                'message' => 'Group image updated successfully.',
                'status' => true,
                'group_image' => $team->group_image
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating the group image.',
                'status' => false,
                'error' => $th->getMessage()
            ], 201);
        }
    }


    /**
     * Update Group Name and Description
     *
     * Updates the name and description of a specified team group.
     * @authenticated
     * @bodyParam team_id int required The ID of the team whose name and description are being updated. Example: 5
     * @bodyParam name string required The new name of the team (maximum 100 characters). Example: "Developers Group"
     * @bodyParam description string required The new description of the team (maximum 255 characters). Example: "A group for discussing development strategies."
     *
     * @response 200 {
     *    "message": "Group name and description updated successfully.",
     *    "status": true,
     *    "name": "Developers Group",
     *    "description": "A group for discussing development strategies.",
     *    "team_id": 5
     * }
     * @response 201 {
     *    "message": "An error occurred while updating the group name and description.",
     *    "status": false
     * }
     */
    public function nameDescriptionUpdate(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:100',
                'description' => 'required|max:255',
            ]);

            $team = Team::find($request->team_id);

            // Check if the team exists
            if (!$team) {
                return response()->json(['message' => 'Team not found', 'status' => false], 201);
            }

            // Update team name and description
            $team->name = $request->name;
            $team->description = $request->description;
            $team->save();

            return response()->json([
                'message' => 'Group name and description updated successfully.',
                'status' => true,
                'name' => $team->name,
                'description' => $team->description,
                'team_id' => $team->id
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating the group name and description.',
                'status' => false,
                'error' => $th->getMessage()
            ], 201);
        }
    }





    //
}
