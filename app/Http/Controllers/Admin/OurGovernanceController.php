<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurGovernance;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

use App\Helpers\Helper;

class OurGovernanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait, CreateSlug;

    public function index(Request $request)
    {
        //  return $request->get('content_country_code', 'US');
        if (auth()->user()->can('Manage Our Governance')) {
            // $our_governances = OurGovernance::orderBy('id', 'desc')->paginate(10);
            $our_governances = OurGovernance::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->paginate(10);
            //   return $our_governances;
            return view('admin.our-governances.list')->with(compact('our_governances'));
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
            $our_governances = OurGovernance::where('country_code', $request->get('content_country_code', 'US'))
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%');
                })
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
        if (auth()->user()->can('Create Our Governance')) {
            return view('admin.our-governances.create');
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
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
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

        $our_governance->country_code = $request->content_country_code ?? 'US';

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
        if (auth()->user()->can('Edit Our Governance')) {
            $our_governance = OurGovernance::find($id);
            return view('admin.our-governances.edit')->with(compact('our_governance'));
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
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_governance->banner_image = $this->imageUpload($request->file('banner_image'), 'our_governances');
        }
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_governance->image = $this->imageUpload($request->file('image'), 'our_governances');
        }

        $our_governance->country_code = $request->content_country_code ?? 'US';

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
        if (auth()->user()->can('Delete Our Governance')) {
            $our_governance = OurGovernance::findOrfail($request->id);
            $our_governance->delete();
            return redirect()->route('our-governances.index')->with('message', 'Our Governance deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
