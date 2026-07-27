<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage About Us Page')) {
            $countryCode = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowForEdit(AboutUs::class, $countryCode);

            return view('admin.about-us.update', [
                'about_us' => $loaded['row'],
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized Access');
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
            'description' => 'required',
        ]);

        $country = $request->content_country_code ?? 'US';

        $about_us = null;
        if ($request->filled('id')) {
            $about_us = AboutUs::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$about_us) {
            $about_us = new AboutUs();
        }

        $about_us->banner_title = $request->banner_title;
        $about_us->description = $request->description;
        $about_us->meta_title = $request->meta_title;
        $about_us->meta_description = $request->meta_description;
        $about_us->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $about_us->banner_image = $this->imageUpload($request->file('banner_image'), 'about-us');
        }

        $attrs = $about_us->getAttributes();
        unset($attrs['id']);
        $about_us = AboutUs::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'About us updated successfully');
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
