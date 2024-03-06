<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrincipalAndBusiness;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class PrincipleAndBusinessController extends Controller
{

    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business = PrincipalAndBusiness::orderBy('id', 'desc')->first();
        return view('admin.principle-and-business.update')->with(compact('business'));
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
            $business = PrincipalAndBusiness::find($request->id);
        } else {
            $business = new PrincipalAndBusiness();
        }

        $business->banner_title = $request->banner_title;
        $business->description = $request->description;
        $business->meta_title = $request->meta_title;
        $business->meta_description = $request->meta_description;
        $business->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $business->banner_image = $this->imageUpload($request->file('banner_image'), 'principle-and-business');
        }

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $business->image = $this->imageUpload($request->file('image'), 'principle-and-business');
        }

        $business->save();

        return redirect()->back()->with('message', 'Principle and Business updated successfully');
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
