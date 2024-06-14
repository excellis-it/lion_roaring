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

    public function page($name, $permission)
    {
        $permission = $permission;
        if (auth()->user()->can($permission)) {
            $name = $name;
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
        return view('user.cms')->with(compact('name', 'permission'));
    }
}
