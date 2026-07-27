<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurGovernance;
use App\Traits\CreateSlug;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

use App\Helpers\Helper;
use App\Models\Country;

class OurGovernanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ImageTrait, CreateSlug;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Our Governance')) {
            $user_country = auth()->user()->country;
            $country = Country::where('id', $user_country)->first();

            $countryCode = Helper::resolveCmsEditCountryCode($request, $country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(OurGovernance::class, $countryCode, 'order_no', 'asc');
            $our_governances = Helper::paginateCollection($loaded['rows'], 10);

            return view('user.admin.our-governances.list', [
                'our_governances' => $our_governances,
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

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            // default sort by order_no asc
            if (!$sort_by) {
                $sort_by = 'order_no';
                $sort_type = 'asc';
            }

            $user_country = auth()->user()->country;
            $country = Country::where('id', $user_country)->first();

            $countryCode = Helper::resolveCmsEditCountryCode($request, $country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(OurGovernance::class, $countryCode, $sort_by, $sort_type);

            $filtered = $loaded['rows']->filter(function ($item) use ($query) {
                if ($query === '' || $query === null) {
                    return true;
                }
                $needle = str_replace('%', ' ', $query);

                return stripos((string) $item->id, $needle) !== false
                    || stripos((string) $item->name, $needle) !== false
                    || stripos((string) $item->slug, $needle) !== false;
            })->values();

            $our_governances = Helper::paginateCollection($filtered, 10);

            return response()->json(['data' => view('user.admin.our-governances.table', [
                'our_governances' => $our_governances,
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
        if (auth()->user()->can('Create Our Governance')) {
            $user_country = auth()->user()->country;
            $country = Country::where('id', $user_country)->first();
            $cmsEditCountryCode = Helper::resolveCmsEditCountryCode(request(), $country->code ?? null);

            return view('user.admin.our-governances.create', [
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
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $slug = $this->createSlug($request->name);
        // check slug is already exist or not
        $is_slug_exist = OurGovernance::where('slug', $slug)->first();
        if ($is_slug_exist) {
            $slug = $slug . '-' . time();
        }

        $user_country = auth()->user()->country;
        $country = Country::where('id', $user_country)->first();

        $our_governance = new OurGovernance();
        $our_governance->name = $request->name;
        $our_governance->slug = $slug;
        $our_governance->description = $request->description;
        $our_governance->meta_title = $request->meta_title;
        $our_governance->meta_description = $request->meta_description;
        $our_governance->meta_keywords = $request->meta_keywords;
        $our_governance->banner_image = $this->imageUpload($request->file('banner_image'), 'our_governances');
        $our_governance->image = $this->imageUpload($request->file('image'), 'our_governances');

        $our_governance->country_code = Helper::resolveCmsEditCountryCode($request, $country->code ?? null);

        // set order_no to be last in that country
        $contentCountryCode = $our_governance->country_code ?? 'US';
        $maxOrder = OurGovernance::where('country_code', $contentCountryCode)->max('order_no') ?? 0;
        $our_governance->order_no = $maxOrder + 1;

        $our_governance->save();




        return redirect()->route('user.admin.our-governances.index')->with('message', 'Our Governance created successfully.');
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
        if (auth()->user()->can('Edit Our Governance')) {
            $our_governance = OurGovernance::findOrFail($id);
            $user_country = auth()->user()->country;
            $country = Country::where('id', $user_country)->first();
            if (!Helper::canSelectCmsContentCountry() && $our_governance->country_code !== ($country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            return view('user.admin.our-governances.edit')->with(compact('our_governance'));
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
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ]);

        $our_governance = OurGovernance::findOrFail($id);
        $user_country = auth()->user()->country;
        $country = Country::where('id', $user_country)->first();
        if (!Helper::canSelectCmsContentCountry() && $our_governance->country_code !== ($country->code ?? null)) {
            abort(403, 'You do not have permission to access this page.');
        }

        if ($our_governance->name != $request->name) {
            $slug = $this->createSlug($request->name);
            $is_slug_exist = OurGovernance::where('slug', $slug)->first();
            if ($is_slug_exist) {
                $slug = $slug . '-' . time();
            }
            $our_governance->slug = $slug;
        }
        $our_governance->name = $request->name;
        $our_governance->description = $request->description;
        $our_governance->meta_title = $request->meta_title;
        $our_governance->meta_description = $request->meta_description;
        $our_governance->meta_keywords = $request->meta_keywords;
        if ($request->hasFile('banner_image')) {
            $request->validate([
                'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_governance->banner_image = $this->imageUpload($request->file('banner_image'), 'our_governances');
        }
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $our_governance->image = $this->imageUpload($request->file('image'), 'our_governances');
        }

        $newCountryCode = Helper::resolveCmsEditCountryCode($request, $country->code ?? null);
        $oldCountryCode = $our_governance->country_code ?? 'US';

        if ($newCountryCode !== $oldCountryCode) {
            // move to last in new country
            $maxOrder = OurGovernance::where('country_code', $newCountryCode)->max('order_no') ?? 0;
            $our_governance->order_no = $maxOrder + 1;

            // update the country_code
            $our_governance->country_code = $newCountryCode;

            // resequence old country
            $this->resequenceCountry($oldCountryCode);
        } else {
            $our_governance->country_code = $newCountryCode;
        }

        $our_governance->save();

        return redirect()->route('user.admin.our-governances.index')->with('message', 'Our Governance updated successfully.');
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
        if (auth()->user()->can('Delete Our Governance')) {
            $our_governance = OurGovernance::findOrfail($request->id);
            $countryCode = $our_governance->country_code ?? 'US';
            $our_governance->delete();

            // resequence after delete
            $this->resequenceCountry($countryCode);

            return redirect()->route('user.admin.our-governances.index')->with('message', 'Our Governance deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Reorder (AJAX)
     */
    public function reorder(Request $request)
    {
        if (!auth()->user()->can('Edit Our Governance')) {
            return response()->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $order = $request->get('order'); // expected array of ids
        $countryCode = $request->get('content_country_code');

        if (!is_array($order)) {
            return response()->json(['status' => false, 'message' => 'Invalid order payload'], 422);
        }

        $position = 1;
        foreach ($order as $id) {
            // skip drafts (no real id yet)
            if (!$id) continue;
            $item = OurGovernance::find($id);
            if (!$item) continue;

            // if country_code provided, only update matching country items
            if ($countryCode && $item->country_code !== $countryCode) continue;

            $item->order_no = $position;
            $item->save();
            $position++;
        }

        return response()->json(['status' => true, 'message' => 'Order updated']);
    }

    /**
     * Resequence order_no for a given country
     */
    protected function resequenceCountry($countryCode)
    {
        $items = OurGovernance::where('country_code', $countryCode)->orderBy('order_no', 'asc')->get();
        $pos = 1;
        foreach ($items as $it) {
            $it->order_no = $pos;
            $it->save();
            $pos++;
        }
    }
}
