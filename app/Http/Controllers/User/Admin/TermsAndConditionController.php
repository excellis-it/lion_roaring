<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
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
            $regionalCode = $this->country->code ?? null;
            $countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
            $loaded = Helper::loadCmsRowForEdit(TermsAndCondition::class, $countryCode);

            return view('user.admin.terms.index', [
                'terms_and_condition' => $loaded['row'],
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

        $country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);

        $terms_and_condition = null;
        if ($request->filled('id')) {
            $terms_and_condition = TermsAndCondition::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$terms_and_condition) {
            $terms_and_condition = new TermsAndCondition();
        }

        $terms_and_condition->text = $request->text;
        $terms_and_condition->description = $request->description;

        $attrs = $terms_and_condition->getAttributes();
        unset($attrs['id']);
        $terms_and_condition = TermsAndCondition::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Terms and Conditions updated successfully');
    }
}
