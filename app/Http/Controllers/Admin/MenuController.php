<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index()
    {
        if(!auth()->user()->can('Manage Menu Settings')){
            abort(403, 'You do not have permission to access this page.');
        }
        $items = MenuItem::orderBy('id')->get();
        return view('user.admin.menu.index', compact('items'));
    }

    public function update(Request $request)
    {
        if(!auth()->user()->can('Manage Menu Settings')){
            abort(403, 'You do not have permission to access this page.');
        }
        $data = $request->get('names', []);
        foreach ($data as $key => $name) {
            $item = MenuItem::where('key', $key)->first();
            if ($item) {
                $item->name = $name;
                $item->save();
            }
        }
        return redirect()->route('admin.menu.index')->with('message', 'Menu names updated');
    }
}
