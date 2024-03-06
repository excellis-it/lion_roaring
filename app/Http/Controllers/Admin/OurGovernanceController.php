<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurGovernance;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class OurGovernanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait, CreateSlug;

    public function index()
    {
        $our_governances = OurGovernance::orderBy('id', 'desc')->paginate(10);
        return view('admin.our-governances.list')->with(compact('our_governances'));
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $our_governances = OurGovernance::where('id', 'like', '%' . $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->orWhere('slug', 'like', '%' . $query . '%')
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);

            return response()->json(['data' => view('admin.our-governances.table', compact('our_governances'))->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.our-governances.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $slug = $this->createSlug($request->name);
        // check slug is already exist or not
        $is_slug_exist = OurGovernance::where('slug', $slug)->first();
        if ($is_slug_exist) {
            $slug = $slug . '-' . time();
        }

        $our_governance = new OurGovernance();
        $our_governance->name = $request->name;
        $our_governance->slug = $slug;
        $our_governance->description = $request->description;
        $our_governance->meta_title = $request->meta_title;
        $our_governance->meta_description = $request->meta_description;
        $our_governance->meta_keywords = $request->meta_keywords;
        $our_governance->banner_image = $this->imageUpload($request->file('banner_image'), 'our_governances');
        $our_governance->image = $this->imageUpload($request->file('image'), 'our_governances');
        $our_governance->save();

        return redirect()->route('our-governances.index')->with('message', 'Our Governance created successfully.');
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
        $our_governance = OurGovernance::find($id);
        return view('admin.our-governances.edit')->with(compact('our_governance'));
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
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $our_governance = OurGovernance::find($id);
        if ($our_governance->name != $request->name) {
            $slug = $this->createSlug($request->name);
            $is_slug_exist = OurGovernance::where('slug', $slug)->first();
            if ($is_slug_exist) {
                $slug = $slug . '-' . time();
            }
            $our_governance->slug = $slug;
        }
        $our_governance->name = $request->name;
        $our_governance->description = $request->description;
        $our_governance->meta_title = $request->meta_title;
        $our_governance->meta_description = $request->meta_description;
        $our_governance->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $our_governance->banner_image = $this->imageUpload($request->file('banner_image'), 'our_governances');
        }
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $our_governance->image = $this->imageUpload($request->file('image'), 'our_governances');
        }
        $our_governance->save();

        return redirect()->route('our-governances.index')->with('message', 'Our Governance updated successfully.');
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

    public function delete(Request $request)
    {
        $our_governance = OurGovernance::findOrfail($request->id);
        $our_governance->delete();
        return redirect()->route('our-governances.index')->with('message', 'Our Governance deleted successfully.');
    }
}
