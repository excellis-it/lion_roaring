<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RegisterAgreement;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class RegisterAgreementController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        if (auth()->user()->can('Manage Register Page Agreement Page')) {
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(RegisterAgreement::class, $countryCode);

            return view('user.admin.register_agreement.update', [
                'agreement' => $loaded['row'],
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
            'agreement_title' => 'required',
            'agreement_description' => 'required',
            'checkbox_text' => 'required',
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $agreement = null;
        if ($request->filled('id')) {
            $agreement = RegisterAgreement::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$agreement) {
            $agreement = new RegisterAgreement();
        }

        $agreement->agreement_title = $request->agreement_title;
        $agreement->agreement_description = $request->agreement_description;
        $agreement->checkbox_text = $request->checkbox_text;
        $agreement->steward_member_1 = $request->steward_member_1;
        $agreement->steward_member_2 = $request->steward_member_2;

        if ($request->hasFile('seal_image')) {
            $agreement->seal_image = $this->imageUpload($request->file('seal_image'), 'agreement_seals', true);
        }

        $attrs = $agreement->getAttributes();
        unset($attrs['id']);
        $agreement = RegisterAgreement::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Register agreement updated successfully');
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
