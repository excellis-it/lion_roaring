<?php

namespace App\Http\Controllers\User\Admin;

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
            if ($this->user_type == 'Global') {
                $faqs = Faq::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'ASC')->paginate(15);
            } else {
                $faqs = Faq::where('country_code', $this->country->code)->orderBy('id', 'ASC')->paginate(15);
            }
            return view('user.admin.faq.list', compact('faqs'));
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
            if ($this->user_type == 'Global') {
                $faqs = Faq::where('country_code', $request->get('content_country_code', 'US'))
                    ->where(function ($q) use ($query) {
                        $q->where('id', 'like', '%' . $query . '%')
                            ->orWhere('question', 'like', '%' . $query . '%')
                            ->orWhere('answer', 'like', '%' . $query . '%');
                    })
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(15);
            } else {
                $faqs = Faq::where('country_code', $this->country->code)
                    ->where(function ($q) use ($query) {
                        $q->where('id', 'like', '%' . $query . '%')
                            ->orWhere('question', 'like', '%' . $query . '%')
                            ->orWhere('answer', 'like', '%' . $query . '%');
                    })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);
            }
            return response()->json(['data' => view('user.admin.faq.table', compact('faqs'))->render()]);
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
            return view('user.admin.faq.create');
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

        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }

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
        $request->validate([
            'question' => "required",
            'answer' => "required",
        ]);

        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
        $faq = Faq::findOrFail($id);
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
            $faq->delete();
            return redirect()->route('user.admin.faq.index')->with('error', 'Faq has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
