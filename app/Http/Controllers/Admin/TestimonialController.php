<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use ImageTrait, CreateSlug;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $testimonials = Testimonial::orderByDesc('id')->paginate(15);
        return view('admin.testimonials.list', compact('testimonials'));
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $testimonials = Testimonial::where('id', 'like', '%' . $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%')
                ->orWhere('address', 'like', '%' . $query . '%')
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('admin.testimonials.table', compact('testimonials'))->render()]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.testimonials.create');
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
            'name' => "required|string|max:255",
            'image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp",
            'description' => 'required',
            'address' => 'required',
        ]);

        $testimonials = new Testimonial();
        $testimonials->name = $request->name;
        $testimonials->address = $request->address;
        $testimonials->description = $request->description;
        $testimonials->image = $this->imageUpload($request->file('image'), 'testimonials');
        $testimonials->save();

        return redirect()->route('testimonials.index')->with('message', 'Testimonial created successfully.');
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
        $testimonial = Testimonial::findOrFail($id);
        return view('admin.testimonials.edit')->with(compact('testimonial'));
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
            'name' => "required|string|max:255",
            'description' => 'required',
            'address' => 'required',
        ]);

        $testimonials = Testimonial::findOrFail($id);
        $testimonials->name = $request->name;
        $testimonials->address = $request->address;
        $testimonials->description = $request->description;
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => "image|mimes:jpeg,png,jpg,gif,svg,webp",
            ]);
            $testimonials->image = $this->imageUpload($request->file('image'), 'testimonials');
        }
        $testimonials->save();

        return redirect()->route('testimonials.index')->with('message', 'Testimonial updated successfully.');
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
        $testimonials = Testimonial::findOrFail($id);
        $testimonials->delete();
        return redirect()->route('testimonials.index')->with('error', 'Testimonial has been deleted successfully.');
    }
}
