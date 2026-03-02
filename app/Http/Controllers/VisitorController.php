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

        // If user is logged in, enforce country restrictions (SUPER ADMIN exempt)
        if (auth()->check() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
            $user = auth()->user();
            $requestedCode = strtoupper($data['country']);

            if ($user->user_type == 'Regional') {
                // Regional user: can only stay on their own country
                $userCountry = \App\Models\Country::find($user->country);
                if ($userCountry && strtoupper($userCountry->code) !== $requestedCode) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are a Regional user. You can only access your assigned country (' . $userCountry->name . ').',
                    ], 403);
                }
            } elseif ($user->user_type == 'Global') {
                // Global user: can only stay on Global
                if ($requestedCode !== 'GL') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are a Global user. You can only access the Global domain.',
                    ], 403);
                }
            }
        }

        $country = strtoupper($data['country'] ?? '');
        $ip = $request->ip();
        $flagSessionKey = 'visitor_country_flag_code_' . $ip;
        $codeSessionKey = 'visitor_country_code_' . $ip;
        $nameSessionKey = 'visitor_country_name_' . $ip;
        $languageSessionKey = 'visitor_country_languages';

        // Store flag key (prevents popup from showing again)
        session([$flagSessionKey => $country]);

        // Handle GLOBAL selection — redirect to main URL with all languages
        if ($country === 'GL') {
            $allLanguages = \App\Models\TranslateLanguage::orderBy('name', 'asc')->get();
            session([
                $codeSessionKey => 'GL',
                $nameSessionKey => 'Global (Main)',
                $languageSessionKey => $allLanguages,
            ]);

            $redirectUrl = \App\Helpers\Helper::getMainUrl();
            return response()->json([
                'status' => 'ok',
                'redirect_url' => $redirectUrl,
            ]);
        }

        // Regular country selection
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

        // Determine the redirect URL dynamically from DB domains
        $redirectUrl = \App\Helpers\Helper::getCountryRedirectUrl($country);

        return response()->json([
            'status' => 'ok',
            'redirect_url' => $redirectUrl,
        ]);
    }
}
