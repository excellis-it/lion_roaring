<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ContactUsCms;
use App\Models\Country;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ContactUsCmsController extends Controller
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
        if (auth()->user()->can('Manage Contact Us Page')) {
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(ContactUsCms::class, $countryCode);

            return view('user.admin.contact-us-cms.update', [
                'contact_us' => $loaded['row'],
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
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
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $contact_us = null;
        if ($request->filled('id')) {
            $contact_us = ContactUsCms::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$contact_us) {
            $contact_us = new ContactUsCms();
        }

        $contact_us->banner_title = $request->banner_title;
        $contact_us->email = $request->email;
        $contact_us->phone = $request->phone;
        $contact_us->address = $request->address;
        $contact_us->title = $request->title;
        $contact_us->description = $request->description;
        if ($request->hasFile('banner_image')) {
            $contact_us->banner_image = $this->imageUpload($request->file('banner_image'), 'contact-us-cms');
        }
        $attrs = $contact_us->getAttributes();
        unset($attrs['id']);
        $contact_us = ContactUsCms::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->route('user.admin.contact-us-cms.index')->with('message', 'Contact Us created successfully.');
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
