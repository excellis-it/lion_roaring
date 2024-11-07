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




    //
}
