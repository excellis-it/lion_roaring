<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use Illuminate\Http\Request;

class BulletinBoardController extends Controller
{


    public function list()
    {
        $user = auth()->user();
        if ($user->can('Manage Bulletin')) {

            $user_type = $user->user_type;
            $user_country = $user->country;
            if (!$user->hasNewRole('SUPER ADMIN')) {
                if ($user_type == 'Global') {
                    $bulletins = Bulletin::orderBy('id', 'desc')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->get();
                } else {
                    $bulletins = Bulletin::orderBy('id', 'desc')->where('country_id', $user_country)->get();
                }
            } else {
                $bulletins = Bulletin::orderBy('id', 'desc')->get();
            }
            return view('user.bulletin-board.list')->with('bulletins', $bulletins);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function load(Request $request)
    {
        $user = auth()->user();
        $user_type = $user->user_type;
        $user_country = $user->country;
        if (!$user->hasNewRole('SUPER ADMIN')) {
            if ($user_type == 'Global') {
                $bulletins = Bulletin::orderBy('id', 'desc')->whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })->get();
            } else {
                $bulletins = Bulletin::orderBy('id', 'desc')->where('country_id', $user_country)->get();
            }
        } else {
            $bulletins = Bulletin::orderBy('id', 'desc')->get();
        }
        return response()->json(['view' => view('user.bulletin-board.show-bulletin')->with('bulletins', $bulletins)->render()]);
    }
}
