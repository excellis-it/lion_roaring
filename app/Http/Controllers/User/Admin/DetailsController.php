<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
        if (auth()->user()->can('Manage Details Page')) {
            if ($this->user_type == 'Global') {
                $details = Detail::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'asc')->get();
            } else {
                $details = Detail::where('country_code', $this->country->code)->orderBy('id', 'asc')->get();
            }
            return view('user.admin.details.update')->with('details', $details);
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

      if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }

        // Determine submitted IDs (if any) and remove only records for this country that aren't submitted
        $submittedIds = $request->image_id ?? [];

        if (is_array($submittedIds) && count($submittedIds) > 0) {
            Detail::where('country_code', $country)->whereNotIn('id', $submittedIds)->delete();
        } else {
            // No IDs submitted => remove all details for this country
            Detail::where('country_code', $country)->delete();
        }

        // Ensure descriptions is an array before iterating
        if (!is_array($request->description)) {
            return redirect()->back()->withErrors('Invalid input for descriptions');
        }

        foreach ($request->description as $key => $value) {
            if (isset($request->image_id[$key]) && $request->image_id[$key]) {
                $detail = Detail::where('country_code', $country)->where('id', $request->image_id[$key])->first();
                if (!$detail) {
                    $detail = new Detail();
                }
            } else {
                $detail = new Detail();
            }

            $detail->description = $value;
            if ($request->hasFile('image') && isset($request->file('image')[$key]) && $request->file('image')[$key]) {
                $detail->image = $this->imageUpload($request->file('image')[$key], 'details');
            }

            $detail->country_code = $country;

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
