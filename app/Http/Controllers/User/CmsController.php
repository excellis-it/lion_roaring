<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MemberPrivacyPolicy;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function memberPrivacyPolicy()
    {
        $policy = MemberPrivacyPolicy::orderBy('id', 'desc')->first();
        return view('user.cms.member_privacy_policy')->with('policy', $policy);
    }

    
}
