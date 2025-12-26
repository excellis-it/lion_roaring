<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
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
        if (auth()->user()->can('Manage Terms and Conditions Page')) {
            if ($this->user_type == 'Global') {
                $terms_and_condition = TermsAndCondition::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $terms_and_condition = TermsAndCondition::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.terms.index')->with(compact('terms_and_condition'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function update(Request $request)
    {
        if (!auth()->user()->can('Manage Terms and Conditions Page')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'text' => 'required',
            'description' => 'required',
        ], [
            'text.required' => 'Terms and Conditions title is required',
            'description.required' => 'Terms and Conditions description is required',
        ]);

        if ($request->id != '') {
            $terms_and_condition = TermsAndCondition::find($request->id);
        } else {
            $terms_and_condition = new TermsAndCondition();
        }

        $terms_and_condition->text = $request->text;
        $terms_and_condition->description = $request->description;
        // $terms_and_condition->save();

        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
        $terms_and_condition = TermsAndCondition::updateOrCreate(['country_code' => $country], array_merge($terms_and_condition->getAttributes(), ['country_code' => $country]));

        return redirect()->back()->with('message', 'Terms and Conditions updated successfully');
    }
}
