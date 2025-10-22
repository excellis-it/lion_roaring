<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;


class ColorController extends Controller
{
    // list/store/edit/update colors
    public function index()
    {
        if (!auth()->user()->can('Manage Estore Colors')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // Logic to list colors
        $colors = Color::where('status', 1)->paginate(10);
        return view('user.estore-colors.list', compact('colors'));
    }

    public function create()
    {
        if (!auth()->user()->can('Create Estore Colors')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // Logic to show create color form
        return view('user.estore-colors.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Estore Colors')) {
            abort(403, 'You do not have permission to access this page.');
        }
        //  return $request;
        // validation
        $request->validate([
            'color_name' => 'required|string|max:255',
            // 'color' => 'required|string', // Assuming color is a hex code
            'status' => 'required|boolean',
        ]);

        // Logic to store new color
        $color = new Color();
        $color->color_name = $request->color_name;
        $color->color = $request->color ?? '#FFFFFF';
        $color->status = $request->status;
        $color->save();

        return redirect()->route('colors.index')->with('message', 'Color created successfully.');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('Edit Estore Colors')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // Logic to show edit color form
        $color = Color::findOrFail($id);
        return view('user.estore-colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('Edit Estore Colors')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // validation
        $request->validate([
            'color_name' => 'required|string|max:255',
            'color' => 'required|string|max:7', // Assuming color is a hex code
            'status' => 'required|boolean',
        ]);

        // Logic to update color
        $color = Color::findOrFail($id);
        $color->color_name = $request->color_name;
        $color->color = $request->color ?? '#FFFFFF';
        $color->status = $request->status;
        $color->save();

        return redirect()->route('colors.index')->with('message', 'Color updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete color
        $color = Color::findOrFail($id);
        $color->delete();

        return redirect()->route('colors.index')->with('message', 'Color deleted successfully.');
    }

    public function delete($id)
    {

        $color = Color::find($id);

        // if color associated with any products, prevent deletion
        if ($color->products()->count() > 0) {
            return response()->json(['success' => false, 'msg' => 'This color is associated with products and cannot be deleted.']);
        }

        if ($color) {
            $color->delete();
            return response()->json(['success' => true, 'msg' => 'Color deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'msg' => 'Color not found.']);
        }
    }
}
