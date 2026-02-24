<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Organization;
use App\Models\OrganizationImage;
use App\Models\OrganizationProject;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;

    public $user_type;
    public $user_country;
    public $country;

    // use consructor
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user_type = auth()->user()->user_type;
            $this->user_country = auth()->user()->country;
            $this->country = Country::where('id', $this->user_country)->first();

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Organizations Page')) {
            if ($this->user_type == 'Global') {
                $organization = Organization::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $organization = Organization::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.organization.update')->with(compact('organization'));
        } else {
            return redirect()->route('user.profile')->with('error', 'You do not have permission to access this page.');
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
        $validator = Validator::make($request->all(), [
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
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validator->after(function ($validator) use ($request) {

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $index => $file) {

                    if (!$file->isValid()) {
                        $validator->errors()->add(
                            "image.$index",
                            "About Section Image " . ($index + 1) . " is invalid."
                        );
                    }

                    if (!in_array($file->extension(), ['jpeg', 'png', 'jpg', 'gif', 'webp'])) {
                        $validator->errors()->add(
                            "image.$index",
                            "About Section Image " . ($index + 1) . " must be jpeg, png, jpg, gif, or webp."
                        );
                    }

                    if ($file->getSize() > 2048 * 1024) {
                        $validator->errors()->add(
                            "image.$index",
                            "About Section Image " . ($index + 1) . " must not exceed 2MB."
                        );
                    }
                }
            }
        });

        $validator->validate();

        if ($request->id != '') {
            $organization = Organization::find($request->id);
        } else {
            $organization = new Organization();
        }

        $organization->banner_title = $request->banner_title;
        $organization->banner_description = $this->cleanText($request->banner_description);
        $organization->meta_title = $request->meta_title;
        $organization->meta_description = $this->cleanText($request->meta_description);
        $organization->meta_keywords = $request->meta_keywords;
        $organization->project_section_title = $request->project_section_title;
        $organization->project_section_sub_title = $request->project_section_sub_title;
        $organization->project_section_description = $this->cleanText($request->project_section_description);
        // second project section fields
        $organization->project_section_two_title = $request->project_section_two_title;
        $organization->project_section_two_sub_title = $request->project_section_two_sub_title;
        $organization->project_section_two_description = $this->cleanText($request->project_section_two_description);
        if ($request->hasFile('banner_image')) {
            if (!empty($organization->banner_image) && Storage::exists($organization->banner_image)) {
                Storage::delete($organization->banner_image);
            }
            $organization->banner_image = $this->imageUpload($request->banner_image, 'organization');
        }


        // $organization->save();

        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
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
                $organization_project->description = $this->cleanText($request->card_description[$key]);
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
                $organization_project->description = $this->cleanText($request->card_description_two[$key]) ?? null;
                $organization_project->section = 2;
                $organization_project->save();
            }
        }

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Organization details updated successfully']);
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

    private function cleanText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5)
            )
        );
    }
}
