<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipPromoCode;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('View Promo Codes')) {
            abort(403, 'Unauthorized');
        }

        $promoCodes = MembershipPromoCode::withCount('usages')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.promo_codes.index', compact('promoCodes'));
    }

    public function create()
    {
        if (!auth()->user()->can('Create Promo Code')) {
            abort(403, 'Unauthorized');
        }

        $tiers = MembershipTier::all();
        $users = User::select('id', 'first_name', 'email')->get();

        return view('user.promo_codes.create', compact('tiers', 'users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Promo Code')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'code' => 'required|string|max:50|unique:membership_promo_codes,code',
            'is_percentage' => 'required|boolean',
            'discount_amount' => 'required|numeric|min:0|max:' . ($request->is_percentage ? '100' : '99999'),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'scope_type' => 'required|in:all_tiers,selected_tiers,all_users,selected_users',
            'tier_ids' => 'nullable|required_if:scope_type,selected_tiers|array',
            'tier_ids.*' => 'exists:membership_tiers,id',
            'user_ids' => 'nullable|required_if:scope_type,selected_users|array',
            'user_ids.*' => 'exists:users,id',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
        ]);

        $data = $request->only([
            'code',
            'is_percentage',
            'discount_amount',
            'start_date',
            'end_date',
            'status',
            'scope_type',
            'usage_limit',
            'per_user_limit',
        ]);

        // Handle scope-specific fields
        if ($request->scope_type === 'selected_tiers') {
            $data['tier_ids'] = $request->tier_ids;
        } else {
            $data['tier_ids'] = null;
        }

        if ($request->scope_type === 'selected_users') {
            $data['user_ids'] = $request->user_ids;
        } else {
            $data['user_ids'] = null;
        }

        MembershipPromoCode::create($data);

        return redirect()->route('user.promo-codes.index')->with('success', 'Promo code created successfully');
    }

    public function edit(MembershipPromoCode $promoCode)
    {
        if (!auth()->user()->can('Edit Promo Code')) {
            abort(403, 'Unauthorized');
        }

        $tiers = MembershipTier::all();
        $users = User::select('id', 'first_name', 'email')->get();

        return view('user.promo_codes.edit', compact('promoCode', 'tiers', 'users'));
    }

    public function update(Request $request, MembershipPromoCode $promoCode)
    {
        if (!auth()->user()->can('Edit Promo Code')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('membership_promo_codes', 'code')->ignore($promoCode->id)],
            'is_percentage' => 'required|boolean',
            'discount_amount' => 'required|numeric|min:0|max:' . ($request->is_percentage ? '100' : '99999'),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'scope_type' => 'required|in:all_tiers,selected_tiers,all_users,selected_users',
            'tier_ids' => 'nullable|required_if:scope_type,selected_tiers|array',
            'tier_ids.*' => 'exists:membership_tiers,id',
            'user_ids' => 'nullable|required_if:scope_type,selected_users|array',
            'user_ids.*' => 'exists:users,id',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
        ]);

        $data = $request->only([
            'code',
            'is_percentage',
            'discount_amount',
            'start_date',
            'end_date',
            'status',
            'scope_type',
            'usage_limit',
            'per_user_limit',
        ]);

        // Handle scope-specific fields
        if ($request->scope_type === 'selected_tiers') {
            $data['tier_ids'] = $request->tier_ids;
        } else {
            $data['tier_ids'] = null;
        }

        if ($request->scope_type === 'selected_users') {
            $data['user_ids'] = $request->user_ids;
        } else {
            $data['user_ids'] = null;
        }

        $promoCode->update($data);

        return redirect()->route('user.promo-codes.index')->with('success', 'Promo code updated successfully');
    }

    public function destroy(MembershipPromoCode $promoCode)
    {
        if (!auth()->user()->can('Delete Promo Code')) {
            abort(403, 'Unauthorized');
        }

        $promoCode->delete();

        return redirect()->route('user.promo-codes.index')->with('success', 'Promo code deleted successfully');
    }

    /**
     * Validate promo code (AJAX endpoint)
     */
    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $promoCode = MembershipPromoCode::where('code', $request->code)->first();

        if (!$promoCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid promo code',
            ], 404);
        }

        $user = auth()->user();

        // Only check user-specific restrictions if user is authenticated
        // During registration, user is not logged in yet
        if ($user && !$promoCode->canBeUsedByUser($user->id)) {
            return response()->json([
                'valid' => false,
                'message' => 'This promo code cannot be used',
            ], 400);
        }

        if (!$promoCode->canBeAppliedToTier($request->tier_id)) {
            return response()->json([
                'valid' => false,
                'message' => 'This promo code is not valid for this membership tier',
            ], 400);
        }

        $tier = MembershipTier::find($request->tier_id);
        $discount = $promoCode->calculateDiscount($tier->cost);
        $finalPrice = max(0, $tier->cost - $discount);

        return response()->json([
            'valid' => true,
            'message' => 'Promo code applied successfully',
            'discount' => $discount,
            'final_price' => $finalPrice,
            'is_percentage' => $promoCode->is_percentage,
            'discount_amount' => $promoCode->discount_amount,
        ]);
    }
}
