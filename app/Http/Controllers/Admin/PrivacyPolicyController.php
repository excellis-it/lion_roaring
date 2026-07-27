<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasNewRole('SUPER ADMIN')) {
            $countryCode = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowForEdit(PrivacyPolicy::class, $countryCode);

            return view('admin.privacy-policy.index', [
                'privacy_policy' => $loaded['row'],
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
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
            'text.required' => 'Privacy Policy title is required',
            'description.required' => 'Privacy Policy description is required',
        ]);

        $country = $request->content_country_code ?? 'US';

        $privacy_policy = null;
        if ($request->filled('id')) {
            $privacy_policy = PrivacyPolicy::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$privacy_policy) {
            $privacy_policy = new PrivacyPolicy();
        }

        $privacy_policy->text = $request->text;
        $privacy_policy->description = $request->description;

        $attrs = $privacy_policy->getAttributes();
        unset($attrs['id']);
        $privacy_policy = PrivacyPolicy::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Privacy Policy updated successfully');
    }
}
