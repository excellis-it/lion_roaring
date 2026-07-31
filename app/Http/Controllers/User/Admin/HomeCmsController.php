<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\HomeCms;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;


class HomeCmsController extends Controller
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
        if (auth()->user()->can('Manage Home Page')) {
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(HomeCms::class, $countryCode);

            return view('user.admin.home.update', [
                'home' => $loaded['row'],
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
        } else {
            return redirect()->route('user.profile')->with('error', 'You do not have the permission to access this page.');
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
        if (!auth()->user()->can('Manage Home Page')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'banner_title' => 'required',
            'banner_image' => 'nullable|mimes:jpeg,jpg,png,gif,webp',
            'banner_video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm',
            'show_banner_image' => 'nullable|boolean',
            'section_1_title' => 'required',
            'section_1_sub_title' => 'required',
            'section_1_description' => 'required',
            'section_3_title' => 'required',
            'section_3_description' => 'required',
            'section_4_title' => 'required',
            'section_4_description' => 'required',
            'section_5_title' => 'required',
            'section_6_title' => 'required',
            'section_6_subtitle' => 'required',
            'section_6_button_text' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $home = null;
        if ($request->filled('id')) {
            $home = HomeCms::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$home) {
            $home = new HomeCms();
        }

        $home->banner_title = $request->banner_title;
        $home->show_banner_image = $request->boolean('show_banner_image');
        $home->section_1_title = $request->section_1_title;
        $home->section_1_sub_title = $request->section_1_sub_title;
        $home->section_1_description = $request->section_1_description;
        $home->section_3_title = $request->section_3_title;
        $home->section_3_description = $request->section_3_description;
        $home->section_4_title = $request->section_4_title;
        $home->section_4_description = $request->section_4_description;
        $home->section_5_title = $request->section_5_title;
        $home->section_6_title = $request->section_6_title;
        $home->section_6_subtitle = $request->section_6_subtitle;
        $home->section_6_button_text = $request->section_6_button_text;
        $home->meta_title = $request->meta_title;
        $home->meta_description = $request->meta_description;
        $home->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $home->banner_image = $this->imageUpload($request->file('banner_image'), 'home');
        }
        if ($request->hasFile('banner_video')) {
            $home->banner_video = $this->imageUpload($request->file('banner_video'), 'home');
        }

        $attrs = $home->getAttributes();
        unset($attrs['id']);
        $attrs = Helper::applyUsCmsMediaDefaults(HomeCms::class, $country, $attrs, ['banner_image', 'banner_video']);
        HomeCms::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Home Page Content Updated Successfully');
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
