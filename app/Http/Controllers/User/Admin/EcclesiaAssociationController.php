<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
        if (auth()->user()->can('Manage Ecclesia Association Page')) {
            if ($this->user_type == 'Global') {
                $ecclesia_association = EcclesiaAssociation::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $ecclesia_association = EcclesiaAssociation::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.ecclesia-associations.update')->with(compact('ecclesia_association'));
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
        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
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
