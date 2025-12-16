<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrincipalAndBusiness;
use App\Models\PrincipleBusinessImage;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrincipleAndBusinessController extends Controller
{

    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Principle and Business Page')) {
            $business = PrincipalAndBusiness::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            $principle_images = PrincipleBusinessImage::get();
            return view('user.admin.principle-and-business.update')->with(compact('business', 'principle_images'));
        } else {
            return redirect()->route('admin.home')->with('error', 'Unauthorized Access');
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
            'description2' => 'required',
            'description3' => 'required',
            'description4' => 'required',
        ]);

        if ($request->id != '') {
            $business = PrincipalAndBusiness::find($request->id);
        } else {
            $business = new PrincipalAndBusiness();
        }

        $business->banner_title = $request->banner_title;
        $business->description = $request->description;
        $business->description1 = $request->description1;
        $business->description2 = $request->description2;
        $business->description3 = $request->description3;
        $business->description4 = $request->description4;
        $business->meta_title = $request->meta_title;
        $business->meta_description = $request->meta_description;
        $business->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $business->banner_image = $this->imageUpload($request->file('banner_image'), 'principle-and-business');
        }
        // $business->save();
        $country = $request->content_country_code ?? 'US';
        $business = PrincipalAndBusiness::updateOrCreate(['country_code' => $country], array_merge($business->getAttributes(), ['country_code' => $country]));

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $request->validate([
                    'image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                ]);
                $principle_image = new PrincipleBusinessImage();
                $principle_image->principle_id = $business->id;
                $principle_image->image = $this->imageUpload($image, 'principle-and-business');
                $principle_image->save();
            }
        }

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

    public function imageDelete(Request $request)
    {
        $principle_image = PrincipleBusinessImage::find($request->id);
        if (!empty($principle_image->image) && Storage::exists($principle_image->image)) {
            Storage::delete($principle_image->image);
        }

        $principle_image->delete();
        return response()->json(['success' => 'Product image deleted successfully.']);
    }
}
