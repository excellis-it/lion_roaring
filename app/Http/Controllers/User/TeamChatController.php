<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChatMember;
use App\Models\Notification;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamMember;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamChatController extends Controller
{
    use ImageTrait;

    public function userLastMessage($team_id, $user_id)
    {
        return TeamChat::where('team_id', $team_id)->whereHas('chatMembers', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->latest()->first();
    }

    public function index()
    {
        if (auth()->user()->can('Manage Team')) {
            // get the team which message is last sent by user
            $teams = Team::with('chats.chatMembers')->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('id', 'desc')->get()->toArray();

            // short by last message userLastMessage function is in Helper.php
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

            // dd($teams);




            $members = User::orderBy('first_name', 'asc')->where('id', '!=', auth()->id())->where('status', true)->get();
            return view('user.team-chat.index')->with(compact('teams', 'members'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:255',
            'members' => 'required|array|min:1', // Ensure members is an array with at least one member
            'members.*' => 'required|exists:users,id', // Ensure each member is a valid user
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
        $count =0;
        foreach ($request->members as $member_id) {

            $team_member = new TeamMember();
            $team_member->team_id = $team->id;
            $team_member->user_id = $member_id;
            $team_member->is_admin = false;
            $team_member->save();
            $count++;
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
        ]);
    }


    public function load(Request $request)
    {
        if ($request->ajax()) {
            $team_id = $request->team_id;
            $team = Team::where('id', $team_id)->with(['members', 'members.user'])->first()->toArray();
            $team_chats = TeamChat::where('team_id', $team_id)->orderBy('created_at', 'asc')->whereHas('chatMembers', function ($query) {
                $query->where('user_id', auth()->id());
            })->with('user')->get();
            ChatMember::where('user_id', auth()->id())->whereHas('chat', function ($query) use ($team_id) {
                $query->where('team_id', $team_id);
            })->update(['is_seen' => true]);
            // team member name with comma separated
            $team_members = TeamMember::where('team_id', $team_id)->where('is_removed', false)->with('user')->get();

            $team_member_name = '';
            foreach ($team_members as $member) {
                $team_member_name .= ($member['user']['first_name'] ?? '') . ' ' .
                    ($member['user']['middle_name'] ?? '') . ' ' .
                    ($member['user']['last_name'] ?? '') . ', ';
            }
            $team_member_name = rtrim($team_member_name, ', ');

            $is_chat = true;
            return response()->json(['view' => (string) view('user.team-chat.chat-body')->with(compact('team', 'team_chats', 'is_chat', 'team_member_name'))]);
        }
    }

    public function send(Request $request)
    {

        $team_chat = new TeamChat();
        $team_chat->team_id = $request->team_id;
        $team_chat->user_id = auth()->id();
        if ($request->file) {
            $team_chat->attachment = $this->imageUpload($request->file('file'), 'team-chat');
        } else {
            $team_chat->message = $request->message;
        }
        $team_chat->save();
        $teams = TeamMember::where('team_id', $request->team_id)->where('is_removed', false)->get();
        $team = Team::find($request->team_id);

        foreach ($teams as $team) {
            $chat_member = new ChatMember();
            $chat_member->chat_id = $team_chat->id;
            $chat_member->user_id = $team->user_id;
            if ($team->user_id == auth()->id()) {
                $chat_member->is_seen = true;
            } else {
                $chat_member->is_seen = false;
            }
            $chat_member->save();

            if ($team->user_id != auth()->id()) {
                $notification = new Notification();
                $notification->user_id = $team->user_id;
                $notification->chat_id = $team_chat->id;
                $notification->message = 'You have a new message in <b>' . $team->name . '</b> group.';
                $notification->type = 'Team';
                $notification->save();
            }
        }
        $chat_member_id = ChatMember::where('chat_id', $team_chat->id)->pluck('user_id')->toArray();



        $chat = TeamChat::where('id', $team_chat->id)->with('user', 'chatMembers')->first();

        return response()->json(['message' => 'Message sent successfully.', 'status' => true, 'chat' => $chat, 'chat_member_id' => $chat_member_id]);
    }

    public function groupInfo(Request $request)
    {
        if ($request->ajax()) {
            $team_id = $request->team_id;
            $team = Team::where('id', $team_id)
                ->with(['members' => function ($query) {
                    $query->where('is_removed', false); // Replace with your condition
                }, 'members.user'])
                ->first();
            $members = User::orderBy('first_name', 'asc')->where('id', '!=', auth()->id())->where('status', true)->get();
            $is_group_info = true;
            return response()->json(['view' => (string) view('user.team-chat.group-info')->with(compact('team', 'is_group_info', 'members'))]);
        }
    }

    public function updateGroupImage(Request $request)
    {
        $request->validate([
            'group_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $team = Team::find($request->team_id);
        $team->group_image = $this->imageUpload($request->file('group_image'), 'team');
        $team->save();


        return response()->json(['message' => 'Group image updated successfully.', 'status' => true, 'group_image' => $team->group_image]);
    }

    public function editNameDes(Request $request)
    {
        $team = Team::where('id', $request->team_id)->select('id', 'name', 'description')->first();
        $group_details = true;
        return response()->json(['status' => true, 'view' => (string) view('user.team-chat.group-details')->with(compact('team', 'group_details'))]);
    }

    public function nameDesUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:255',
        ]);

        $team = Team::find($request->team_id);
        $team->name = $request->name;
        $team->description = $request->description;
        $team->save();

        return response()->json(['message' => 'Group name and description updated successfully.', 'status' => true, 'name' => $team->name, 'description' => $team->description, 'team_id' => $team->id]);
    }

    public function removeMember(Request $request)
    {
        $team_id = $request->team_id;
        $user_id = $request->user_id;

        $team_member = TeamMember::where('team_id', $team_id)->where('user_id', $user_id)->first();
        $team_member->is_removed = true;
        $team_member->is_removed_at = now();
        $team_member->save();

        // remove message sent by this user
        $team_chat = new TeamChat();
        $team_chat->team_id = $team_id;
        $team_chat->user_id = $user_id;
        $team_chat->message = $team_member->user->first_name . ' ' . $team_member->user->last_name . ' has been removed from the group.';
        $team_chat->save();

        $members = TeamMember::where('team_id', $team_id)->where('is_removed', false)->get();

        // notificaion
        $notification = new Notification();
        $notification->user_id = $user_id;
        $notification->message = 'You have been removed from <b>' . Team::find($team_id)->name . '</b> group.';
        $notification->type = 'Team';
        $notification->save();

        foreach ($members as $team) {
            $chat_member = new ChatMember();
            $chat_member->chat_id = $team_chat->id;
            $chat_member->user_id = $team->user_id;
            if ($team->user_id == auth()->id()) {
                $chat_member->is_seen = true;
            } else {
                $chat_member->is_seen = false;
            }
            $chat_member->save();
        }

        $chat_member_id = ChatMember::where('chat_id', $team_chat->id)->pluck('user_id')->toArray();

        $chat = TeamChat::where('id', $team_chat->id)->with('user')->first();

        return response()->json(['message' => 'Member removed successfully.', 'status' => true, 'team_id' => $team_id, 'user_id' => $user_id, 'chat' => $chat, 'chat_member_id' => $chat_member_id, 'notification' => $notification]);
    }

    public function groupList(Request $request)
    {
        if ($request->ajax()) {
            $teams = Team::with('chats.chatMembers')->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('id', 'desc')->get()->toArray();

            // short by last message userLastMessage function is in Helper.php
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
            if ($request->team_id) {
                $team_id = $request->team_id;
                return response()->json(['view' => (string) view('user.team-chat.group-list')->with(compact('teams', 'team_id'))]);
            } else {
                return response()->json(['view' => (string) view('user.team-chat.group-list')->with(compact('teams'))]);
            }
        }
    }

    public function exitFromGroup(Request $request)
    {
        $team_id = $request->team_id;
        $user_id = auth()->id();

        $team_member = TeamMember::where('team_id', $team_id)->where('user_id', $user_id)->first();
        $team_member->is_removed = true;
        $team_member->is_removed_at = now();
        $team_member->save();

        // check how many admin in this group
        $admin_count = TeamMember::where('team_id', $team_id)->where('is_admin', true)->where('is_removed', false)->count();

        // if admin count is 0 add admin to first member
        if ($admin_count == 0) {
            $team_member = TeamMember::where('team_id', $team_id)->where('is_removed', false)->first();
            if ($team_member) {
                $team_member->is_admin = true;
                $team_member->save();
            }
        }

        $team_members = TeamMember::where('team_id', $team_id)->where('is_removed', false)->with('user')->get();
        $team_member_name = '';
        foreach ($team_members as $member) {
            $team_member_name .= ($member['user']['first_name'] ?? '') . ' ' .
                ($member['user']['middle_name'] ?? '') . ' ' .
                ($member['user']['last_name'] ?? '') . ', ';
        }
        $team_member_name = rtrim($team_member_name, ', ');

        // team member count
        $team_member_count = TeamMember::where('team_id', $team_id)->where('is_removed', false)->get();

        // delete team is no member in group
        if ($team_member_count->count() == 0) {
            $team = Team::find($team_id);
            $team->delete();

            $team_delete = true;
        } else {
            $team_delete = false;
        }

        $team_member_id = TeamMember::where('team_id', $team_id)->pluck('user_id')->toArray();

        return response()->json(['message' => 'You have left the group successfully.', 'status' => true, 'team_id' => $team_id, 'user_id' => $user_id, 'team_member_name' => $team_member_name, 'team_delete' => $team_delete, 'team_member_id' => $team_member_id]);
    }

    public function addMemberTeam(Request $request)
    {
        $request->validate([
            'members' => 'required',
            'members.*' => 'required',
        ]);

        $team_id = $request->team_id;
        $only_added_members = $request->members;
        if ($request->members) {
            $already_member_arr = [];
            foreach ($request->members as $member) {
                if (TeamMember::where('team_id', $team_id)->where('user_id', $member)->where('is_removed', true)->exists()) {
                    $team_member = TeamMember::where('team_id', $team_id)->where('user_id', $member)->where('is_removed', true)->first();
                    $team_member->is_removed = false;
                    $team_member->is_removed_at = null;
                    $team_member->save();
                    $already_member_arr[] = $team_member->user_id;
                } else {
                    $team_member = new TeamMember();
                    $team_member->team_id = $team_id;
                    $team_member->user_id = $member;
                    $team_member->is_admin = false;
                    $team_member->save();
                }

                $notification = new Notification();
                $notification->user_id = $member;
                $notification->message = 'You have been added to <b>' . Team::find($team_id)->name . '</b> group.';
                $notification->type = 'Team';
                $notification->save();
            }
        }

        $team_members = TeamMember::where('team_id', $team_id)->where('is_removed', false)->with('user')->get();

        $team_member_name = '';
        foreach ($team_members as $member) {
            $team_member_name .= ($member['user']['first_name'] ?? '') . ' ' .
                ($member['user']['middle_name'] ?? '') . ' ' .
                ($member['user']['last_name'] ?? '') . ', ';
        }

        $team_member_name = rtrim($team_member_name, ', ');

        $new_team_members = TeamMember::where('team_id', $team_id)->where('is_removed', false)->whereIn('user_id', $only_added_members)->with('user')->get();
        $only_added_members_name = '';
        foreach ($new_team_members as $member) {
            $only_added_members_name .= ($member['user']['first_name'] ?? '') . ' ' .
                ($member['user']['middle_name'] ?? '') . ' ' .
                ($member['user']['last_name'] ?? '') . ', ';
        }

        $only_added_members_name = rtrim($only_added_members_name, ', ');
        // chat message
        $team_chat = new TeamChat();
        $team_chat->team_id = $team_id;
        $team_chat->user_id = auth()->id();
        $team_chat->message = 'New members added to the group. ' . $only_added_members_name;
        $team_chat->save();

        foreach ($team_members as $member) {
            $chat_member = new ChatMember();
            $chat_member->chat_id = $team_chat->id;
            $chat_member->user_id = $member->user_id;
            if ($member->user_id == auth()->id()) {
                $chat_member->is_seen = true;
            } else {
                $chat_member->is_seen = false;
            }
            $chat_member->save();
        }

        $chat_member_id = ChatMember::where('chat_id', $team_chat->id)->pluck('user_id')->toArray();

        $chat = TeamChat::where('id', $team_chat->id)->with('user')->first();

        return response()->json(['message' => 'Members added successfully.', 'status' => true, 'team_id' => $team_id, 'team_member_name' => $team_member_name, 'chat' => $chat, 'chat_member_id' => $chat_member_id, 'already_member_arr' => $already_member_arr, 'only_added_members' => $only_added_members]);
    }

    public function deleteGroup(Request $request)
    {
        $team_id = $request->team_id;
        $team = Team::find($team_id);

        $team_member_id = TeamMember::where('team_id', $team_id)->pluck('user_id')->toArray();
        $team->delete();

        TeamMember::where('team_id', $team_id)->delete();
        TeamChat::where('team_id', $team_id)->delete();


        return response()->json(['message' => 'Group deleted successfully.', 'status' => true, 'team_id' => $team_id, 'team_member_id' => $team_member_id]);
    }

    public function makeAdmin(Request $request)
    {
        $team_id = $request->team_id;
        $user_id = $request->user_id;

        $team_member = TeamMember::where('team_id', $team_id)->where('user_id', $user_id)->first();
        $team_member->is_admin = true;
        $team_member->save();

        $notification = new Notification();
        $notification->user_id = $user_id;
        $notification->message = 'You have been made admin of <b>' . Team::find($team_id)->name . '</b> group.';
        $notification->type = 'Team';
        $notification->save();

        return response()->json(['message' => 'Member made admin successfully.', 'status' => true, 'team_id' => $team_id, 'user_id' => $user_id, 'notification' => $notification]);
    }

    public function seen(Request $request)
    {
        $chat_id = $request->chat_id;

        $chat_member = ChatMember::where('chat_id', $chat_id)->where('user_id', auth()->id())->first();
        $chat_member->is_seen = true;
        $chat_member->save();

        return response()->json(['message' => 'Message seen successfully.', 'status' => true]);
    }

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $team_id = $request->team_id;
            $chat_id = $request->chat_id;
            $user_id = auth()->id();
            $notification_check = Notification::where('user_id', $user_id)->where('chat_id', $chat_id)->where('type', 'Team')->first();
            if (isset($request->is_delete)) {
                $notification_check->is_read = 1;
                $notification_check->is_delete = 1;
                $notification_check->update();
            }
            if ($notification_check) {
                $notification_count = Notification::where('user_id', $user_id)->where('is_read', 0)->where('is_delete', 0)->count();
                return response()->json(['message' => 'Notification read successfully.', 'status' => true, 'notification' => $notification_check, 'notification_count' => $notification_count]);
            } else {
                $notification = new Notification();
                $notification->user_id = $user_id;
                $notification->chat_id = $chat_id;
                $notification->message = 'You have a new message in <b>' . Team::find($team_id)->name . '</b> group.';
                $notification->type = 'Team';
                $notification->save();
                $notification_count = Notification::where('user_id', $user_id)->where('is_read', 0)->where('is_delete', 0)->count();
                return response()->json(['message' => 'Notification sent successfully.', 'status' => true, 'notification' => $notification, 'notification_count' => $notification_count]);
            }
        }

        return abort(404); // Optional: return a 404 response if not an AJAX request
    }

    public function removeChat(Request $request)
    {
        $chat_id = $request->chat_id;
        $del_from = $request->del_from;
        $team_id = $request->team_id;

        $team_chat = TeamChat::find($chat_id);
        if ($del_from == 'everyone') {
            $last_message = Helper::userLastMessage($team_id, auth()->id());
            if ($last_message->id == $chat_id) {
                $last_message = true;
            } else {
                $last_message = false;
            }
            $team_chat->delete();
        } else {
            $last_message = Helper::userLastMessage($team_id, auth()->id());
            if ($last_message->id == $chat_id) {
                $last_message = true;
            } else {
                $last_message = false;
            }
            $chat_member = ChatMember::where('chat_id', $chat_id)->where('user_id', auth()->id())->first();
            $chat_member->delete();
        }

        return response()->json(['message' => 'Chat removed successfully.', 'status' => true, 'chat_id' => $chat_id, 'last_message' => $last_message]);
    }

    public function clearAllConversation(Request $request)
    {
        $team_id = $request->team_id;
        ChatMember::whereHas('chat', function ($query) use ($team_id) {
            $query->where('team_id', $team_id);
        })->delete();
        TeamChat::where('team_id', $team_id)->delete();
        return response()->json(['message' => 'All conversation cleared successfully.', 'status' => true]);
    }
}
