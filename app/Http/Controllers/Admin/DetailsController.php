<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Detail;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class DetailsController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = Detail::orderBy('id', 'asc')->get();
        return view('admin.details.update')->with('details', $details);
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
        foreach ($request->description as $key => $value) {
            if (isset($request->image_id[$key])) {
                $detail = Detail::find($request->image_id[$key]);
                // delete old image
                Detail::whereNotIn('id', $request->image_id)->delete();
            } else {
                $detail = new Detail();
            }

            $detail->description = $value;
            if (isset($request->file('image')[$key]) && $request->hasFile('image') && $request->file('image')[$key]) {
                $detail->image = $this->imageUpload($request->file('image')[$key], 'details');
            }

            $detail->save();
        }

        return redirect()->back()->with('message', 'Details updated successfully');
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
