<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    // list/store/edit/update sizes
    public function index()
    {
        // Logic to list sizes
        $sizes = Size::where('status', 1)->paginate(10);
        return view('user.estore-sizes.list', compact('sizes'));
    }

    public function create()
    {
        // Logic to show create size form
        return view('user.estore-sizes.create');
    }

    public function store(Request $request)
    {
        // Logic to store new size
        $size = new Size();
        $size->size = $request->name;
        $size->save();

        return redirect()->route('sizes.index')->with('message', 'Size created successfully.');
    }

    public function edit($id)
    {
        // Logic to show edit size form
        $size = Size::findOrFail($id);
        return view('user.estore-sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update size
        $size = Size::findOrFail($id);
        $size->size = $request->name;
        $size->save();

        return redirect()->route('sizes.index')->with('message', 'Size updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete size
        $size = Size::findOrFail($id);
        $size->delete();

        return redirect()->route('sizes.index')->with('message', 'Size deleted successfully.');
    }

    public function delete($id)
    {

        $size = Size::find($id);
        if ($size) {
            $size->delete();
            return redirect()->route('sizes.index')->with('message', 'Size deleted successfully.');
        } else {
            return redirect()->route('sizes.index')->with('error', 'File not found.');
        }
    }
}
