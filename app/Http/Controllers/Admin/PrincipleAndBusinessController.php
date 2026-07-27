<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
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
            $countryCode = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowForEdit(PrincipalAndBusiness::class, $countryCode);
            $business = $loaded['row'];
            $principle_images = $business && $business->id
                ? PrincipleBusinessImage::where('principle_id', $business->id)->get()
                : collect();

            return view('admin.principle-and-business.update', [
                'business' => $business,
                'principle_images' => $principle_images,
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
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

        $country = $request->content_country_code ?? 'US';

        $business = null;
        if ($request->filled('id')) {
            $business = PrincipalAndBusiness::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$business) {
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
        $attrs = $business->getAttributes();
        unset($attrs['id']);
        $business = PrincipalAndBusiness::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

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
