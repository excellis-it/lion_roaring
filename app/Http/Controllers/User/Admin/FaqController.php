<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
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
        if (auth()->user()->can('Manage Faq')) {
            $countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(Faq::class, $countryCode, 'id', 'asc');
            $faqs = Helper::paginateCollection($loaded['rows'], 15);

            return view('user.admin.faq.list', [
                'faqs' => $faqs,
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
            $sort_type = $request->get('sorttype') ?: 'asc';
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
            $loaded = Helper::loadCmsRowsForEdit(Faq::class, $countryCode, $sort_by, $sort_type);

            $filtered = $loaded['rows']->filter(function ($faq) use ($query) {
                if ($query === '' || $query === null) {
                    return true;
                }
                $needle = str_replace('%', ' ', $query);

                return stripos((string) $faq->id, $needle) !== false
                    || stripos((string) $faq->question, $needle) !== false
                    || stripos((string) $faq->answer, $needle) !== false;
            })->values();

            $faqs = Helper::paginateCollection($filtered, 15);

            return response()->json(['data' => view('user.admin.faq.table', [
                'faqs' => $faqs,
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
        if (auth()->user()->can('Create Faq')) {
            $cmsEditCountryCode = Helper::resolveCmsEditCountryCode(request(), $this->country->code ?? null);

            return view('user.admin.faq.create', [
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
        if (!auth()->user()->can('Create Faq')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'question' => "required",
            'answer' => "required",
        ]);

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->country_code = $country;
        $faq->save();

        return redirect()->route('user.admin.faq.index')->with('message', 'Faq created successfully.');
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
        if (auth()->user()->can('Edit Faq')) {
            $faq = Faq::findOrFail($id);
            if (!Helper::canSelectCmsContentCountry() && $faq->country_code !== ($this->country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            return view('user.admin.faq.edit')->with(compact('faq'));
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
        if (!auth()->user()->can('Edit Faq')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'question' => "required",
            'answer' => "required",
        ]);

        $faq = Faq::findOrFail($id);
        if (!Helper::canSelectCmsContentCountry() && $faq->country_code !== ($this->country->code ?? null)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->country_code = $country;
        $faq->save();

        return redirect()->route('user.admin.faq.index')->with('message', 'Faq updated successfully.');
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
        if (auth()->user()->can('Delete Faq')) {
            $faq = Faq::findOrFail($id);
            if (!Helper::canSelectCmsContentCountry() && $faq->country_code !== ($this->country->code ?? null)) {
                abort(403, 'You do not have permission to access this page.');
            }

            $faq->delete();
            return redirect()->route('user.admin.faq.index')->with('error', 'Faq has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
