<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $privacy_policy = PrivacyPolicy::orderBy('id', 'desc')->first();
            return view('admin.privacy-policy.index')->with(compact('privacy_policy'));
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
            'text.required' => 'Privacy Policy title is required',
            'description.required' => 'Privacy Policy description is required',
        ]);

        if ($request->id != '') {
            $privacy_policy = PrivacyPolicy::find($request->id);
        } else {
            $privacy_policy = new PrivacyPolicy();
        }

        $privacy_policy->text = $request->text;
        $privacy_policy->description = $request->description;
        $privacy_policy->save();

        return redirect()->back()->with('message', 'Privacy Policy updated successfully');
    }
}
