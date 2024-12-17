<?php

namespace App\Http\Controllers\Admin;

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
    public function index()
    {
        if (auth()->user()->can('Manage Pages')) {
        $agreement = RegisterAgreement::orderBy('id', 'desc')->first();
        return view('admin.register_agreement.update', compact('agreement'));
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
            'agreement_description' => 'required'
        ]);

        if ($request->id != '') {
            $agreement = RegisterAgreement::find($request->id);
        } else {
            $agreement = new RegisterAgreement();
        }

        $agreement->agreement_title = $request->agreement_title;
        $agreement->agreement_description = $request->agreement_description;
        $agreement->save();

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
