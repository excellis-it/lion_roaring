<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Gallery;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class GalleryController extends Controller
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
        if (auth()->user()->can('Manage Gallery')) {
            $countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(Gallery::class, $countryCode, 'id', 'desc');
            $gallery = Helper::paginateCollection($loaded['rows'], 15);

            return view('user.admin.gallery.list', [
                'gallery' => $gallery,
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
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
        if (auth()->user()->can('Create Gallery')) {
            $cmsEditCountryCode = Helper::resolveCmsEditCountryCode(request(), $this->country->code ?? null);

            return view('user.admin.gallery.create', [
                'cmsEditCountryCode' => $cmsEditCountryCode,
            ]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'image.*' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp",
        ], [
            'image.*.required' => 'Please select an image.',
            'image.*.image' => 'Please select an image.',
            'image.*.mimes' => 'Please select an image.',
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        foreach ($request->image as $key => $value) {
            $gallery = new Gallery();
            $gallery->image = $this->imageUpload($request->file('image')[$key], 'gallery');
            $gallery->country_code = $country;
            $gallery->save();
        }
        return redirect()->route('user.admin.gallery.index')->with('message', 'Gallery created successfully.');
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
        if (auth()->user()->can('Edit Gallery')) {
            $gallery = Gallery::findOrFail($id);
            if (!Helper::canSelectCmsContentCountry() && $gallery->country_code !== ($this->country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            return view('user.admin.gallery.edit')->with(compact('gallery'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
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
        $request->validate([
            'image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp",
        ]);

        $gallery = Gallery::findOrFail($id);
        if (!Helper::canSelectCmsContentCountry() && $gallery->country_code !== ($this->country->code ?? null)) {
            abort(403, 'You do not have permission to access this page.');
        }

        if ($request->hasFile('image')) {
            $gallery->image = $this->imageUpload($request->file('image'), 'gallery');
        }
        $gallery->country_code = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
        $gallery->save();

        return redirect()->route('user.admin.gallery.index')->with('message', 'Gallery updated successfully.');
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

    public function delete($id)
    {
        if (auth()->user()->can('Delete Gallery')) {
            $gallery = Gallery::findOrFail($id);
            $gallery->delete();
            return redirect()->route('user.admin.gallery.index')->with('error', 'Gallery has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
