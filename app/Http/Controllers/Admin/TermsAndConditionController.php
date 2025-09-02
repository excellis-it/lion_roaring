<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $terms_and_condition = TermsAndCondition::orderBy('id', 'desc')->first();
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
        ],[
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
        $terms_and_condition->save();

        return redirect()->back()->with('message', 'Terms and Conditions updated successfully');
    }
}
