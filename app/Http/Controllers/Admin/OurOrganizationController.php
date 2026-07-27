<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
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

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Our Organization')) {
            $code = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowsForEdit(OurOrganization::class, $code, 'id', 'desc');
            $our_organizations = Helper::paginateCollection($loaded['rows'], 10);

            return view('admin.our-organizations.list', [
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

            $code = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowsForEdit(OurOrganization::class, $code, $sort_by, $sort_type);

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

            return response()->json(['data' => view('admin.our-organizations.table', [
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
            return view('admin.our-organizations.create');
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
        // dd($request->all());
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
        $our_organization->country_code = $request->content_country_code ?? 'US';

        $our_organization->save();

        return redirect()->route('our-organizations.index')->with('message', 'Our Organization created successfully.');
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
            $our_organization = OurOrganization::find($id);
            return view('admin.our-organizations.edit')->with(compact('our_organization'));
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
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:our_organizations,slug,' . $id,
        ]);

        $our_organization = OurOrganization::find($id);
        $our_organization->slug = $request->slug;
        $our_organization->name = $request->name;
        $our_organization->description = $request->description;
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_organization->image = $this->imageUpload($request->file('image'), 'our_organizations');
        }
        $our_organization->country_code = $request->content_country_code ?? 'US';
        $our_organization->save();

        return redirect()->route('our-organizations.index')->with('message', 'Our Organization updated successfully.');
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
            $our_organization->delete();
            return redirect()->route('our-organizations.index')->with('message', 'Our Organization deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
