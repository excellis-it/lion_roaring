<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PmaTerm;
use Illuminate\Http\Request;

class PmaDisclaimerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage PMA Terms Page')) {
            $term = PmaTerm::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('admin.pma-disclaimer.update')->with(compact('term'));
        } else {
            abort(403, 'You do not have permission to access this page.');
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
            'title' => 'required',
            'description' => 'required',
            'checkbox_text' => 'nullable|string',
        ]);

        if ($request->id != '') {
            $terms = PmaTerm::find($request->id);
        } else {
            $terms = new PmaTerm();
        }

        $terms->title = $request->title;
        $terms->description = $request->description;
        $terms->checkbox_text = $request->checkbox_text;
        // $terms->save();
        $country = $request->content_country_code ?? 'US';
        $terms = PmaTerm::updateOrCreate(['country_code' => $country], array_merge($terms->getAttributes(), ['country_code' => $country]));

        return redirect()->back()->with('message', 'Terms and Conditions updated successfully');
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
