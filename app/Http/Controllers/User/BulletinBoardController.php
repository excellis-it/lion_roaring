<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use Illuminate\Http\Request;

class BulletinBoardController extends Controller
{
    public function list()
    {
        $bulletins = Bulletin::orderBy('id', 'desc')->get();
        return view('user.bulletin-board.list')->with('bulletins', $bulletins);
    }

    public function load(Request $request)
    {
        $bulletins = Bulletin::orderBy('id', 'desc')->get();
        return response()->json(['view' => view('user.bulletin-board.show-bulletin')->with('bulletins', $bulletins)->render()]);
    }
}
