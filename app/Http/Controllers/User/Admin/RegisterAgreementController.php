<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RegisterAgreement;
use Illuminate\Http\Request;

class RegisterAgreementController extends Controller
{
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
            if ($this->user_type == 'Global') {
                $agreement = RegisterAgreement::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $agreement = RegisterAgreement::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
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
        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
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
