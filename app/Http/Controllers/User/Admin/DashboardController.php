<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // same Global/regional split the donations listing uses
        $scoped = fn() => auth()->user()->user_type == 'Global'
            ? Donation::query()
            : Donation::where('country_id', auth()->user()->country);

        $count['today_donation'] = $scoped()->whereDate('created_at', date('Y-m-d'))->sum('donation_amount');
        $count['total_donation_this_month'] = $scoped()->whereMonth('created_at', date('m'))->sum('donation_amount');
        $count['total_donation'] = $scoped()->sum('donation_amount');
        $count['total_donation_this_year'] = $scoped()->whereYear('created_at', date('Y'))->sum('donation_amount');
        return view('user.user.profile')->with(compact('count'));
    }
}
