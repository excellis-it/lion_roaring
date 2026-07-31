<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\OurOrganization;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class OurOrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait, CreateSlug;

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
        if (auth()->user()->can('Manage Our Organization')) {
            $countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(OurOrganization::class, $countryCode, 'id', 'desc');
            $our_organizations = Helper::paginateCollection($loaded['rows'], 10);

            return view('user.admin.our-organizations.list', [
                'our_organizations' => $our_organizations,
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby') ?: 'id';
            $sort_type = $request->get('sorttype') ?: 'desc';
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(OurOrganization::class, $countryCode, $sort_by, $sort_type);

            $filtered = $loaded['rows']->filter(function ($item) use ($query) {
                if ($query === '' || $query === null) {
                    return true;
                }
                $needle = str_replace('%', ' ', $query);

                return stripos((string) $item->id, $needle) !== false
                    || stripos((string) $item->name, $needle) !== false
                    || stripos((string) $item->slug, $needle) !== false;
            })->values();

            $our_organizations = Helper::paginateCollection($filtered, 10);

            return response()->json(['data' => view('user.admin.our-organizations.table', [
                'our_organizations' => $our_organizations,
                'isUsPrefill' => $loaded['isUsPrefill'],
            ])->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('Create Our Organization')) {
            $cmsEditCountryCode = Helper::resolveCmsEditCountryCode(request(), $this->country->code ?? null);

            return view('user.admin.our-organizations.create', [
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
        if (!auth()->user()->can('Create Our Organization')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'slug' => 'required|unique:our_organizations,slug',
        ]);

        $our_organization = new OurOrganization();
        $our_organization->name = $request->name;
        $our_organization->slug = $request->slug;
        $our_organization->description = $request->description;
        $our_organization->image = $this->imageUpload($request->file('image'), 'our_organizations');
        $our_organization->country_code = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
        $our_organization->save();

        return redirect()->route('user.admin.our-organizations.index')->with('message', 'Our Organization created successfully.');
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
        if (auth()->user()->can('Edit Our Organization')) {
            $our_organization = OurOrganization::findOrFail($id);
            if (!Helper::canSelectCmsContentCountry() && $our_organization->country_code !== ($this->country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            return view('user.admin.our-organizations.edit')->with(compact('our_organization'));
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
        if (!auth()->user()->can('Edit Our Organization')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:our_organizations,slug,' . $id,
        ]);

        $our_organization = OurOrganization::findOrFail($id);
        if (!Helper::canSelectCmsContentCountry() && $our_organization->country_code !== ($this->country->code ?? null)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $our_organization->slug = $request->slug;
        $our_organization->name = $request->name;
        $our_organization->description = $request->description;
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_organization->image = $this->imageUpload($request->file('image'), 'our_organizations');
        }
        $our_organization->country_code = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
        $our_organization->save();

        return redirect()->route('user.admin.our-organizations.index')->with('message', 'Our Organization updated successfully.');
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

    public function delete(Request $request)
    {
        if (auth()->user()->can('Delete Our Organization')) {
            $our_organization = OurOrganization::findOrfail($request->id);
            if (!Helper::canSelectCmsContentCountry() && $our_organization->country_code !== ($this->country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            $our_organization->delete();
            return redirect()->route('user.admin.our-organizations.index')->with('message', 'Our Organization deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
