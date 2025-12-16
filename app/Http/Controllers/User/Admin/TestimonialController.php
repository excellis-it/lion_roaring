<?php

namespace App\Http\Controllers\User\Admin;

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


    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Testimonials')) {
            // $testimonials = Testimonial::orderByDesc('id')->paginate(15);
            $testimonials = Testimonial::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->paginate(10);
            return view('user.admin.testimonials.list', compact('testimonials'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $testimonials = Testimonial::where('country_code', $request->get('content_country_code', 'US'))
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('address', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.admin.testimonials.table', compact('testimonials'))->render()]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('Create Testimonials')) {
            return view('user.admin.testimonials.create');
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
        $testimonials->country_code = $request->content_country_code ?? 'US';
        $testimonials->save();

        return redirect()->route('user.admin.testimonials.index')->with('message', 'Testimonial created successfully.');
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
        if (auth()->user()->can('Edit Testimonials')) {
            $testimonial = Testimonial::findOrFail($id);
            return view('user.admin.testimonials.edit')->with(compact('testimonial'));
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
        $testimonials->country_code = $request->content_country_code ?? 'US';
        $testimonials->save();

        return redirect()->route('user.admin.testimonials.index')->with('message', 'Testimonial updated successfully.');
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
        if (auth()->user()->can('Delete Testimonials')) {
            $testimonials = Testimonial::findOrFail($id);
            $testimonials->delete();
            return redirect()->route('user.admin.testimonials.index')->with('error', 'Testimonial has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
