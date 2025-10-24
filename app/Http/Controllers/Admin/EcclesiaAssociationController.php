<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Ecclesia Association Page')) {
            $ecclesia_association = EcclesiaAssociation::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('admin.ecclesia-associations.update')->with(compact('ecclesia_association'));
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
            'description1' => 'required',
        ], [
            'description1.required' => 'The partner page content is required.'
        ]);

        if ($request->id != '') {
            $ecclesia_association = EcclesiaAssociation::find($request->id);
        } else {
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
        // $ecclesia_association->save();

        $country = $request->content_country_code ?? 'US';
        $ecclesia_association = EcclesiaAssociation::updateOrCreate(['country_code' => $country], array_merge($ecclesia_association->getAttributes(), ['country_code' => $country]));

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
