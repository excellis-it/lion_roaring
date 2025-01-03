<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Gallery')) {
            $gallery = Gallery::orderByDesc('id')->paginate(15);
            return view('admin.gallery.list', compact('gallery'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('Create Gallery')) {
            return view('admin.gallery.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'image.*' => "required|image|mimes:jpeg,png,jpg,gif,svg",
        ], [
            'image.*.required' => 'Please select an image.',
            'image.*.image' => 'Please select an image.',
            'image.*.mimes' => 'Please select an image.',
        ]);
        foreach ($request->image as $key => $value) {
            $gallery = new Gallery();
            $gallery->image = $this->imageUpload($request->file('image')[$key], 'gallery');
            $gallery->save();
        }
        return redirect()->route('gallery.index')->with('message', 'Gallery created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->can('Edit Gallery')) {
            $gallery = Gallery::findOrFail($id);
            return view('admin.gallery.edit')->with(compact('gallery'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => "required|image|mimes:jpeg,png,jpg,gif,svg",
        ]);

        $gallery = Gallery::findOrFail($id);
        if ($request->hasFile('image')) {
            $gallery->image = $this->imageUpload($request->file('image'), 'gallery');
        }
        $gallery->save();

        return redirect()->route('gallery.index')->with('message', 'Gallery updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete Gallery')) {
            $gallery = Gallery::findOrFail($id);
            $gallery->delete();
            return redirect()->route('gallery.index')->with('error', 'Gallery has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
