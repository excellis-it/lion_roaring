<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\MembershipMeasurement;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::with('benefits')->get();
        $measurement = MembershipMeasurement::first();
        return view('frontend.membership.index', compact('tiers', 'measurement'));
    }
}
