<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use Illuminate\Http\Request;

class BulletinBoardController extends Controller
{


    public function list()
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        if ($user_type == 'Global') {
            $bulletins = Bulletin::orderBy('id', 'desc')->get();
        } else {
            $bulletins = Bulletin::orderBy('id', 'desc')->where('country_id', $user_country)->get();
        }
        return view('user.bulletin-board.list')->with('bulletins', $bulletins);
    }

    public function load(Request $request)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        if ($user_type == 'Global') {
            $bulletins = Bulletin::orderBy('id', 'desc')->get();
        } else {
            $bulletins = Bulletin::orderBy('id', 'desc')->where('country_id', $user_country)->get();
        }
        return response()->json(['view' => view('user.bulletin-board.show-bulletin')->with('bulletins', $bulletins)->render()]);
    }
}
