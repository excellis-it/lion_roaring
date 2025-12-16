<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index()
    {
        $items = MenuItem::orderBy('id')->get();
        return view('user.admin.menu.index', compact('items'));
    }

    public function update(Request $request)
    {
        $data = $request->get('names', []);
        foreach ($data as $key => $name) {
            $item = MenuItem::where('key', $key)->first();
            if ($item) {
                $item->name = $name;
                $item->save();
            }
        }
        return redirect()->route('user.admin.menu.index')->with('message', 'Menu names updated');
    }
}
