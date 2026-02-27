<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function setCountry(Request $request)
    {
        $data = $request->validate([
            'country' => 'required|string|max:10',
        ]);

        $country = strtoupper($data['country'] ?? '');
        $ip = $request->ip();
        $flagSessionKey = 'visitor_country_flag_code_' . $ip;
        $codeSessionKey = 'visitor_country_code_' . $ip;
        $nameSessionKey = 'visitor_country_name_' . $ip;
        $languageSessionKey = 'visitor_country_languages';

        // Store flag key (prevents popup from showing again)
        session([$flagSessionKey => $country]);

        // Also update the main country session keys so content & dropdown reflect the selection
        $countryData = \App\Models\Country::with('languages')->where('code', $country)->first();
        $languages = $countryData ? $countryData->languages : collect();

        // Ensure English is included
        $hasEnglish = $languages instanceof \Illuminate\Support\Collection
            ? $languages->contains(fn($lang) => strtolower($lang->code ?? '') === 'en')
            : false;
        if (!$hasEnglish) {
            $english = \App\Models\TranslateLanguage::whereRaw('LOWER(code) = ?', ['en'])->first();
            if ($english) {
                $languages = $languages instanceof \Illuminate\Support\Collection
                    ? $languages->push($english)
                    : collect([$english]);
            }
        }

        session([
            $codeSessionKey => $country,
            $nameSessionKey => $countryData->name ?? 'United States',
            $languageSessionKey => $languages,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
