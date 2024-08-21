<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamMember;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class TeamChatController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Team')) {
            $teams = Team::with('lastMessage')->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('id', 'desc')->get();


            $members = User::orderBy('first_name', 'asc')->where('id', '!=', auth()->id())->get();
            return view('user.team-chat.index')->with(compact('teams', 'members'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:255',
            'members' => 'required',
            'members.*' => 'required',
            'group_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $team = new Team();
        $team->name = $request->name;
        $team->description = $request->description;
        $team->group_image = $this->imageUpload($request->file('group_image'), 'team');
        $team->save();

        $team_member = new TeamMember();
        $team_member->team_id = $team->id;
        $team_member->user_id = auth()->id();
        $team_member->is_admin = true;
        $team_member->save();

        if ($request->members) {
            foreach ($request->members as $member) {
                $team_member = new TeamMember();
                $team_member->team_id = $team->id;
                $team_member->user_id = $member;
                $team_member->is_admin = false;
                $team_member->save();
            }
        }

        $team_chat = new TeamChat();
        $team_chat->team_id = $team->id;
        $team_chat->user_id = auth()->id();
        $team_chat->message = 'Welcome to ' . $team->name . ' group.';
        $team_chat->save();

        $team = Team::with('lastMessage')->find($team->id);

        return response()->json(['message' => 'Team created successfully.', 'status' => true, 'team' => $team]);
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $team_id = $request->team_id;
            $team = Team::where('id', $team_id)->with(['members', 'members.user'])->first()->toArray();
            $team_chats = TeamChat::where('team_id', $team_id)->orderBy('created_at', 'asc')->get();
            // team member name with comma separated
            $team_member_name = '';
            foreach ($team['members'] as $member) {
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

        $chat = TeamChat::where('id', $team_chat->id)->with('user')->first();

        return response()->json(['message' => 'Message sent successfully.', 'status' => true, 'chat' => $chat]);
    }

    public function groupInfo(Request $request)
    {
        if ($request->ajax()) {
            $team_id = $request->team_id;
            $team = Team::where('id', $team_id)->with(['members', 'members.user'])->first();
            $is_group_info = true;
            return response()->json(['view' => (string) view('user.team-chat.group-info')->with(compact('team','is_group_info'))]);
        }
    }
}
