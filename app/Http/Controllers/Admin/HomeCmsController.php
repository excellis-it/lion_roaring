<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeCms;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class HomeCmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Home Page')) {
            $home = HomeCms::orderBy('id', 'desc')->first();
            return view('admin.home.update')->with('home', $home);
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have the permission to access this page.');
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
        // $table->string('banner_title')->nullable();
        //     $table->string('banner_image')->nullable();
        //     $table->string('banner_video')->nullable();
        //     $table->string('section_1_title')->nullable();
        //     $table->string('section_1_sub_title')->nullable();
        //     $table->string('section_1_video')->nullable();
        //     $table->longText('section_1_description')->nullable();
        //     $table->string('section_2_left_title')->nullable();
        //     $table->string('section_2_left_image')->nullable();
        //     $table->longText('section_2_left_description')->nullable();
        //     $table->string('section_2_right_title')->nullable();
        //     $table->string('section_2_right_image')->nullable();
        //     $table->longText('section_2_right_description')->nullable();
        //     $table->string('section_3_title')->nullable();
        //     $table->longText('section_3_description')->nullable();
        //     $table->string('section_4_title')->nullable();
        //     $table->longText('section_4_description')->nullable();
        //     $table->string('section_5_title')->nullable();
        //     $table->string('meta_title')->nullable();
        //     $table->longText('meta_description')->nullable();
        //     $table->string('meta_keywords')->nullable();

        $request->validate([
            'banner_title' => 'required',
            'banner_image' => 'nullable|mimes:jpeg,jpg,png,gif',
            'banner_video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm',
            'section_1_title' => 'required',
            'section_1_sub_title' => 'required',
            'section_1_video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm',
            'section_1_description' => 'required',
            'section_2_left_title' => 'required',
            'section_2_left_image' => 'nullable|mimes:jpeg,jpg,png,gif',
            'section_2_left_description' => 'required',
            'section_2_right_title' => 'required',
            'section_2_right_image' => 'nullable|mimes:jpeg,jpg,png,gif',
            'section_2_right_description' => 'required',
            'section_3_title' => 'required',
            'section_3_description' => 'required',
            'section_4_title' => 'required',
            'section_4_description' => 'required',
            'section_5_title' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        if ($request->id != '') {
            $home = HomeCms::find($request->id);
        } else {
            $home = new HomeCms();
        }

        $home->banner_title = $request->banner_title;
        $home->section_1_title = $request->section_1_title;
        $home->section_1_sub_title = $request->section_1_sub_title;
        $home->section_1_description = $request->section_1_description;
        $home->section_2_left_title = $request->section_2_left_title;
        $home->section_2_left_description = $request->section_2_left_description;
        $home->section_2_right_title = $request->section_2_right_title;
        $home->section_2_right_description = $request->section_2_right_description;
        $home->section_3_title = $request->section_3_title;
        $home->section_3_description = $request->section_3_description;
        $home->section_4_title = $request->section_4_title;
        $home->section_4_description = $request->section_4_description;
        $home->section_5_title = $request->section_5_title;
        $home->meta_title = $request->meta_title;
        $home->meta_description = $request->meta_description;
        $home->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $home->banner_image = $this->imageUpload($request->file('banner_image'), 'home');
        }
        if ($request->hasFile('banner_video')) {
            $home->banner_video = $this->imageUpload($request->file('banner_video'), 'home');
        }
        if ($request->hasFile('section_1_video')) {
            $home->section_1_video = $this->imageUpload($request->file('section_1_video'), 'home');
        }
        if ($request->hasFile('section_2_left_image')) {
            $home->section_2_left_image = $this->imageUpload($request->file('section_2_left_image'), 'home');
        }
        if ($request->hasFile('section_2_right_image')) {
            $home->section_2_right_image = $this->imageUpload($request->file('section_2_right_image'), 'home');
        }
        $home->save();

        return redirect()->back()->with('message', 'Home Page Content Updated Successfully');
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
