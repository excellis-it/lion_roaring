<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Faq')) {
            $code = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowsForEdit(Faq::class, $code, 'id', 'asc');
            $faqs = Helper::paginateCollection($loaded['rows'], 15);

            return view('admin.faq.list', [
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

            $code = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowsForEdit(Faq::class, $code, $sort_by, $sort_type);

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

            return response()->json(['data' => view('admin.faq.table', [
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
            return view('admin.faq.create');
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
            'question' => "required",
            'answer' => "required",
        ]);

        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->country_code = $request->content_country_code ?? 'US';
        $faq->save();

        return redirect()->route('faq.index')->with('message', 'Faq created successfully.');
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
            return view('admin.faq.edit')->with(compact('faq'));
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
            'question' => "required",
            'answer' => "required",
        ]);

        $faq = Faq::findOrFail($id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->country_code = $request->content_country_code ?? 'US';
        $faq->save();

        return redirect()->route('faq.index')->with('message', 'Faq updated successfully.');
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
            $faq->delete();
            return redirect()->route('faq.index')->with('error', 'Faq has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
