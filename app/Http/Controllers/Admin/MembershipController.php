<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTier;
use App\Models\MembershipBenefit;
use App\Models\MembershipMeasurement;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\User;

class MembershipController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::with('benefits')->get();
        $measurement = MembershipMeasurement::first();
        return view('admin.membership.index', compact('tiers', 'measurement'));
    }

    public function create()
    {
        return view('admin.membership.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_tiers,slug',
        ]);
        $tier = MembershipTier::create($request->only(['name', 'slug', 'description', 'cost']));
        $benefits = $request->input('benefits', []);
        foreach ($benefits as $i => $b) {
            if (!empty($b)) {
                MembershipBenefit::create(['tier_id' => $tier->id, 'benefit' => $b, 'sort_order' => $i]);
            }
        }
        return redirect()->route('admin.membership.index')->with('success', 'Tier created');
    }

    public function edit(MembershipTier $membership)
    {
        $tier = $membership->load('benefits');
        return view('admin.membership.edit', compact('tier'));
    }

    public function update(Request $request, MembershipTier $membership)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $membership->update($request->only(['name', 'slug', 'description', 'cost']));
        // update benefits
        MembershipBenefit::where('tier_id', $membership->id)->delete();
        $benefits = $request->input('benefits', []);
        foreach ($benefits as $i => $b) {
            if (!empty($b)) {
                MembershipBenefit::create(['tier_id' => $membership->id, 'benefit' => $b, 'sort_order' => $i]);
            }
        }
        return redirect()->route('admin.membership.index')->with('success', 'Tier updated');
    }

    public function delete(MembershipTier $membership)
    {
        $membership->delete();
        return redirect()->route('admin.membership.index')->with('success', 'Tier removed');
    }

    public function settings(Request $request)
    {
        $measurement = MembershipMeasurement::first();
        if ($request->isMethod('post')) {
            $data = $request->only('label', 'description', 'yearly_dues');
            if ($measurement) {
                $measurement->update($data);
            } else {
                MembershipMeasurement::create($data);
            }
            return redirect()->route('admin.membership.index')->with('success', 'Measurement updated');
        }
        return view('admin.membership.settings', compact('measurement'));
    }

    public function members(Request $request)
    {
        $date_after =  '2025-11-01';
        $members = UserSubscription::where('created_at', '>', $date_after)->with(['user', 'payments'])->orderBy('subscription_start_date', 'desc')->paginate(20);
        return view('admin.membership.members', compact('members'));
    }

    public function memberPayments(User $user)
    {
        $date_after =  '2025-11-01';
        $payments = SubscriptionPayment::where('user_id', $user->id)->where('created_at', '>', $date_after)->with('userSubscription')->orderBy('id', 'desc')->get();
        return view('admin.membership.payments', compact('payments', 'user'));
    }

    public function payments(Request $request)
    {
        $date_after =  '2025-11-01';
        $payments = SubscriptionPayment::where('created_at', '>', $date_after)->with(['user', 'userSubscription'])->orderBy('id', 'desc')->paginate(20);
        return view('admin.membership.payments_all', compact('payments'));
    }
}
