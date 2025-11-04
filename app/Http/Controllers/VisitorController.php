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

        $country = strtolower($data['country'] ?? '');
        $ip = $request->ip();
        $sessionKey = 'visitor_country_flag_code_' . $ip;

        // store uppercase code to match existing usage (optional)
        session([$sessionKey => strtoupper($country)]);

        return response()->json(['status' => 'ok']);
    }
}
