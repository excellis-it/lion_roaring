<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUsCms;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ContactUsCmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;
    public function index()
    {
        if (auth()->user()->can('Manage Contact Us Page')) {
            $contact_us = ContactUsCms::orderBy('id', 'desc')->first();
            return view('admin.contact-us-cms.update')->with(compact('contact_us'));
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
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($request->id != '') {
            $contact_us = ContactUsCms::find($request->id);
        } else {
            $contact_us = new ContactUsCms();
        }

        $contact_us->banner_title = $request->banner_title;
        $contact_us->email = $request->email;
        $contact_us->phone = $request->phone;
        $contact_us->address = $request->address;
        $contact_us->title = $request->title;
        $contact_us->description = $request->description;
        if ($request->hasFile('banner_image')) {
            $contact_us->banner_image = $this->imageUpload($request->file('banner_image'), 'contact-us-cms');
        }
        $contact_us->save();

        return redirect()->route('contact-us-cms.index')->with('message', 'Contact Us created successfully.');
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
