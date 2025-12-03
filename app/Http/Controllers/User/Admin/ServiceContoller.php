<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurOrganization;
use App\Models\Service;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ServiceContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Services')) {
            $our_organization = OurOrganization::where('slug', $request->slug)->first();
            $our_organization_id = $our_organization->id;
            $services = Service::where('our_organization_id', $our_organization_id)->get();
            return view('user.admin.service.update')->with(compact('services', 'our_organization_id'));
        } else {
            return redirect()->back()->with('message', 'You do not have permission to access this page.');
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
        for ($key = 0; $key < $request->column_count; $key++) {
            if (isset($request->image_id[$key])) {
                $service = Service::find($request->image_id[$key]);
                // delete old image
                Service::whereNotIn('id', $request->image_id)->delete();
            } else {
                $service = new Service();
            }

            $service->content = $request->content[$key] ?? '';
            $service->our_organization_id = $request->our_organization_id;
            if (isset($request->file('image')[$key]) && $request->hasFile('image') && $request->file('image')[$key]) {
                $service->image = $this->imageUpload($request->file('image')[$key], 'details');
            }

            $service->save();
        }

        return redirect()->back()->with('message', 'Services updated successfully');
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
