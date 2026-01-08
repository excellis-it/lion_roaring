<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasNewRole('SUPER ADMIN')) {
            $terms_and_condition = TermsAndCondition::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('admin.terms.index')->with(compact('terms_and_condition'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function update(Request $request)
    {
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

        $country = $request->content_country_code ?? 'US';
        $terms_and_condition = TermsAndCondition::updateOrCreate(['country_code' => $country], array_merge($terms_and_condition->getAttributes(), ['country_code' => $country]));

        return redirect()->back()->with('message', 'Terms and Conditions updated successfully');
    }
}
