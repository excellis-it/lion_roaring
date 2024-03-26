<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationCenter;
use App\Models\OurOrganization;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class OrganizationCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait, CreateSlug;

    public function index()
    {
        $organization_centers = OrganizationCenter::orderBy('id', 'desc')->paginate(10);
        return view('admin.organization-centers.list')->with(compact('organization_centers'));
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $organization_centers = OrganizationCenter::where('id', 'like', '%' . $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->orWhere('slug', 'like', '%' . $query . '%')
                ->orWhereHas('ourOrganization', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);

            return response()->json(['data' => view('admin.organization-centers.table', compact('organization_centers'))->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = OurOrganization::orderBy('name', 'asc')->get();
        return view('admin.organization-centers.create')->with(compact('organizations'));
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
            'our_organization_id' => 'required', // 'our_organization_id' => 'required
            'name' => 'required',
            'description' => 'nullable',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $slug = $this->createSlug($request->name);
        // check slug is already exist or not
        $is_slug_exist = OrganizationCenter::where('slug', $slug)->first();
        if ($is_slug_exist) {
            $slug = $slug . '-' . time();
        }

        $organization_center = new OrganizationCenter();
        $organization_center->our_organization_id = $request->our_organization_id;
        $organization_center->name = $request->name;
        $organization_center->slug = $slug;
        $organization_center->description = $request->description;
        $organization_center->meta_title = $request->meta_title;
        $organization_center->meta_description = $request->meta_description;
        $organization_center->meta_keywords = $request->meta_keywords;
        $organization_center->banner_image = $this->imageUpload($request->file('banner_image'), 'organization_centers');
        $organization_center->image = $this->imageUpload($request->file('image'), 'organization_centers');
        $organization_center->save();

        return redirect()->route('organization-centers.index')->with('message', 'Organization Center created successfully.');
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
        $organizations = OurOrganization::orderBy('name', 'asc')->get();
        $organization_center = OrganizationCenter::find($id);
        return view('admin.organization-centers.edit')->with(compact('organization_center', 'organizations'));
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
            'our_organization_id' => 'required', // 'our_organization_id' => 'required
            'name' => 'required',
            'description' => 'nullable',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $organization_center = OrganizationCenter::find($id);
        if ($organization_center->name != $request->name) {
            $slug = $this->createSlug($request->name);
            $is_slug_exist = OrganizationCenter::where('slug', $slug)->first();
            if ($is_slug_exist) {
                $slug = $slug . '-' . time();
            }
            $organization_center->slug = $slug;
        }
        $organization_center->our_organization_id = $request->our_organization_id;
        $organization_center->name = $request->name;
        $organization_center->description = $request->description;
        $organization_center->meta_title = $request->meta_title;
        $organization_center->meta_description = $request->meta_description;
        $organization_center->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $organization_center->banner_image = $this->imageUpload($request->file('banner_image'), 'organization_centers');
        }
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $organization_center->image = $this->imageUpload($request->file('image'), 'organization_centers');
        }
        $organization_center->save();

        return redirect()->route('organization-centers.index')->with('message', 'Organization Center updated successfully.');
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
        $organization_center = OrganizationCenter::findOrfail($request->id);
        $organization_center->delete();
        return redirect()->route('organization-centers.index')->with('message', 'Organization Center deleted successfully.');
    }
}
