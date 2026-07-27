<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
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
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(PrivacyPolicy::class, $countryCode);

            return view('user.admin.privacy-policy.index', [
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

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

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
