<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Register Page Agreement Page')) {
            $countryCode = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowForEdit(RegisterAgreement::class, $countryCode);

            return view('admin.register_agreement.update', [
                'agreement' => $loaded['row'],
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
            'agreement_title' => 'required',
            'agreement_description' => 'required',
            'checkbox_text' => 'required',
        ]);

        $country = $request->content_country_code ?? 'US';

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
