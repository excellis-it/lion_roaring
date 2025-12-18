<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisterAgreement;
use Illuminate\Http\Request;

class RegisterAgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Register Page Agreement Page')) {
            $agreement = RegisterAgreement::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('user.admin.register_agreement.update', compact('agreement'));
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

        if ($request->id != '') {
            $agreement = RegisterAgreement::find($request->id);
        } else {
            $agreement = new RegisterAgreement();
        }

        $agreement->agreement_title = $request->agreement_title;
        $agreement->agreement_description = $request->agreement_description;
        $agreement->checkbox_text = $request->checkbox_text;
        // $agreement->save();
        $country = $request->content_country_code ?? 'US';
        $agreement = RegisterAgreement::updateOrCreate(['country_code' => $country], array_merge($agreement->getAttributes(), ['country_code' => $country]));

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
