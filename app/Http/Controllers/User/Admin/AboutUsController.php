<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Country;
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
        if (auth()->user()->can('Manage About Us Page')) {
            if ($this->user_type == 'Global') {
                $about_us = AboutUs::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $about_us = AboutUs::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.about-us.update')->with(compact('about_us'));
        } else {
            return redirect()->route('user.profile')->with('error', 'Unauthorized Access');
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

        if ($request->id != '') {
            $about_us = AboutUs::find($request->id);
        } else {
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
        // $about_us->save();
       if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
        $about_us = AboutUs::updateOrCreate(['country_code' => $country], array_merge($about_us->getAttributes(), ['country_code' => $country]));

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
