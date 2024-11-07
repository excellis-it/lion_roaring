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



    //
}
