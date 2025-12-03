<?php

namespace App\Http\Controllers\User\Admin;

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

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Organizations Page')) {
            $organization = Organization::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('user.admin.organization.update')->with(compact('organization'));
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
            'project_section_two_title' => 'nullable',
            'project_section_two_sub_title' => 'nullable',
            'project_section_two_description' => 'nullable',
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
        // second project section fields
        $organization->project_section_two_title = $request->project_section_two_title;
        $organization->project_section_two_sub_title = $request->project_section_two_sub_title;
        $organization->project_section_two_description = $request->project_section_two_description;
        if ($request->hasFile('banner_image')) {
            if (!empty($organization->banner_image) && Storage::exists($organization->banner_image)) {
                Storage::delete($organization->banner_image);
            }
            $organization->banner_image = $this->imageUpload($request->banner_image, 'organization');
        }


        // $organization->save();

        $country = $request->content_country_code ?? 'US';
        $organization = Organization::updateOrCreate(['country_code' => $country], array_merge($organization->getAttributes(), ['country_code' => $country]));

        if ($request->image) {
            foreach ($request->image as $key => $image) {
                $organization_image = new OrganizationImage();
                $organization_image->organization_id = $organization->id;
                $organization_image->image = $this->imageUpload($image, 'organization');
                $organization_image->save();
            }
        }

        if ($request->card_title) {
            OrganizationProject::where('organization_id', $organization->id)->where('section', 1)->delete();
            foreach ($request->card_title as $key => $title) {
                $organization_project = new OrganizationProject();
                $organization_project->organization_id = $organization->id;
                $organization_project->title = $title;
                $organization_project->description = $request->card_description[$key];
                $organization_project->section = 1;
                $organization_project->save();
            }
        }

        // process second section cards
        if ($request->card_title_two) {
            OrganizationProject::where('organization_id', $organization->id)->where('section', 2)->delete();
            foreach ($request->card_title_two as $key => $title) {
                $organization_project = new OrganizationProject();
                $organization_project->organization_id = $organization->id;
                $organization_project->title = $title;
                $organization_project->description = $request->card_description_two[$key] ?? null;
                $organization_project->section = 2;
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
