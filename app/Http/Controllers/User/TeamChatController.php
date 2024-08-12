<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamChatController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('Manage Team')) {
            $teams = Team::whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })->get();
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
    }
}
