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
        if (!auth()->user()->can('Manage Estore Sizes')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $sizes = Size::where('status', 1)->paginate(10);
        return view('user.estore-sizes.list', compact('sizes'));
    }

    public function create()
    {

        // Logic to show create size form
        if (!auth()->user()->can('Create Estore Sizes')) {
            abort(403, 'You do not have permission to access this page.');
        }
        return view('user.estore-sizes.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Estore Sizes')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // validate
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Logic to store new size
        $size = new Size();
        $size->size = $request->name;
        $size->status = $request->status;
        $size->save();

        return redirect()->route('sizes.index')->with('message', 'Size created successfully.');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('Edit Estore Sizes')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // Logic to show edit size form
        $size = Size::findOrFail($id);
        return view('user.estore-sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('Edit Estore Sizes')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // Logic to update size
        $size = Size::findOrFail($id);

        $size->size = $request->name;
        $size->status = $request->status;
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

        // if size associated with any products, prevent deletion
        if ($size->products()->count() > 0) {
            return response()->json(['success' => false, 'msg' => 'This size is associated with products and cannot be deleted.']);
        }

        if ($size) {
            $size->delete();
            return response()->json(['success' => true, 'msg' => 'Size deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'msg' => 'Size not found.']);
        }
    }
}
