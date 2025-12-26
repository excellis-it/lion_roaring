<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
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
        if (auth()->user()->can('Manage Privacy Policy Page')) {
            if ($this->user_type == 'Global') {
                $privacy_policy = PrivacyPolicy::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $privacy_policy = PrivacyPolicy::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.privacy-policy.index')->with(compact('privacy_policy'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function update(Request $request)
    {
        if (!auth()->user()->can('Manage Privacy Policy Page')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'text' => 'required',
            'description' => 'required',
        ], [
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
        // $privacy_policy->save();

        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
        $privacy_policy = PrivacyPolicy::updateOrCreate(['country_code' => $country], array_merge($privacy_policy->getAttributes(), ['country_code' => $country]));

        return redirect()->back()->with('message', 'Privacy Policy updated successfully');
    }
}
