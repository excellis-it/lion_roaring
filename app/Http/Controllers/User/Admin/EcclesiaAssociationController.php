<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\EcclesiaAssociation;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class EcclesiaAssociationController extends Controller
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
        if (auth()->user()->can('Manage Ecclesia Association Page')) {
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(EcclesiaAssociation::class, $countryCode);

            return view('user.admin.ecclesia-associations.update', [
                'ecclesia_association' => $loaded['row'],
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
            'description' => 'required',
            'description1' => 'required',
        ], [
            'description1.required' => 'The partner page content is required.'
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $ecclesia_association = null;
        if ($request->filled('id')) {
            $ecclesia_association = EcclesiaAssociation::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$ecclesia_association) {
            $ecclesia_association = new EcclesiaAssociation();
        }

        $ecclesia_association->banner_title = $request->banner_title;
        $ecclesia_association->description = $request->description;
        $ecclesia_association->description1 = $request->description1;
        $ecclesia_association->meta_title = $request->meta_title;
        $ecclesia_association->meta_description = $request->meta_description;
        $ecclesia_association->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $ecclesia_association->banner_image = $this->imageUpload($request->file('banner_image'), 'ecclesia-association');
        }
        $attrs = $ecclesia_association->getAttributes();
        unset($attrs['id']);
        $attrs = Helper::applyUsCmsMediaDefaults(EcclesiaAssociation::class, $country, $attrs, ['banner_image']);
        $ecclesia_association = EcclesiaAssociation::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Ecclesia Association updated successfully');
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
