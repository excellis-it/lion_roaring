<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationImage;
use App\Models\OrganizationProject;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;

    public function index()
    {
        $organization = Organization::orderBy('id', 'desc')->first();
        return view('admin.organization.update')->with(compact('organization'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'banner_title' => 'required',
            'banner_description' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
            'project_section_title' => 'required',
            'project_section_sub_title' => 'required',
            'project_section_description' => 'required',
        ]);

        if ($request->id != '') {
            $organization = Organization::find($request->id);
        } else {
            $organization = new Organization();
        }

        $organization->banner_title = $request->banner_title;
        $organization->banner_description = $request->banner_description;
        $organization->meta_title = $request->meta_title;
        $organization->meta_description = $request->meta_description;
        $organization->meta_keywords = $request->meta_keywords;
        $organization->project_section_title = $request->project_section_title;
        $organization->project_section_sub_title = $request->project_section_sub_title;
        $organization->project_section_description = $request->project_section_description;
        $organization->save();

        if ($request->image) {
            foreach ($request->image as $key => $image) {
                $organization_image = new OrganizationImage();
                $organization_image->organization_id = $organization->id;
                $organization_image->image = $this->imageUpload($image, 'organization');
                $organization_image->save();
            }
        }

        if ($request->card_title) {
            OrganizationProject::where('organization_id', $organization->id)->delete();
           foreach ($request->card_title as $key => $title) {
                $organization_project = new OrganizationProject();
                $organization_project->organization_id = $organization->id;
                $organization_project->title = $title;
                $organization_project->description = $request->card_description[$key];
                $organization_project->save();
            }
        }

        return redirect()->back()->with('message', 'Organization details updated successfully');
    }

    public function imageDelete(Request $request)
    {
        $organization_image = OrganizationImage::find($request->id);
        if (!empty($organization_image->image) && Storage::exists($organization_image->image)) {
            Storage::delete($organization_image->image);
        }

        $organization_image->delete();
        return response()->json(['success' => 'Product image deleted successfully.']);
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
        //
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
        //
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
}
