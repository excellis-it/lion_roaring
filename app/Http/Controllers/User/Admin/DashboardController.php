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
        $count['today_donation'] = Donation::whereDate('created_at', date('Y-m-d'))->sum('donation_amount');
        $count['total_donation_this_month'] = Donation::whereMonth('created_at', date('m'))->sum('donation_amount');
        $count['total_donation'] = Donation::sum('donation_amount');
        $count['total_donation_this_year'] = Donation::whereYear('created_at', date('Y'))->sum('donation_amount');
        return view('user.admin.dashboard')->with(compact('count'));
    }

}
