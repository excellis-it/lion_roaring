<?php

namespace App\Services;

use App\Models\EstorePromoCode;
use App\Models\MembershipPromoCode;
use App\Models\MembershipTier;
use Carbon\Carbon;

/**
 * Unified promo-code validator for both E-Store and Membership flows.
 *
 * E-Store promo codes (EstorePromoCode) and Membership promo codes (MembershipPromoCode)
 * have meaningfully different scopes and constraints, so this class exposes two entry
 * points rather than pretending they're interchangeable.
 *
 * Result shape (both methods):
 *   [
 *     'valid' => bool,
 *     'message' => string,
 *     'code' => ?string,
 *     'discount_amount' => float,   // absolute currency amount to subtract
 *     'is_percentage' => bool,
 *     'original_price' => float,
 *     'final_price' => float,
 *     'promo_code' => ?Model,       // underlying EstorePromoCode|MembershipPromoCode
 *   ]
 */
class PromoCodeValidator
{
    /**
     * Validate an E-Store promo code against the current cart.
     *
     * @param array $cartItems  Items shaped like ['product_id'=>int,'subtotal'=>float,...]
     */
    public static function validateEstore(string $code, ?int $userId, array $cartItems): array
    {
        $promo = EstorePromoCode::where('code', $code)
            ->where('status', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->first();

        if (!$promo) {
            return self::failed('Promo code not found or expired.');
        }

        $cart = collect($cartItems);
        $subtotal = (float) $cart->sum('subtotal');
        $eligibleSubtotal = $subtotal;

        switch ($promo->scope_type) {
            case 'selected_users':
                if (!$userId || !in_array($userId, $promo->user_ids ?? [])) {
                    return self::failed('This promo code is not applicable for your account.');
                }
                break;

            case 'selected_products':
                $cartProductIds = $cart->pluck('product_id')->toArray();
                $applicable = array_intersect($cartProductIds, $promo->product_ids ?? []);
                if (empty($applicable)) {
                    return self::failed('This promo code is not applicable for items in your cart.');
                }
                $eligibleSubtotal = (float) $cart
                    ->filter(fn ($item) => in_array($item['product_id'], $applicable))
                    ->sum('subtotal');
                break;
        }

        if (!$promo->is_percentage && $eligibleSubtotal < (float) $promo->discount_amount) {
            return self::failed(
                'Cart total must be at least $' . number_format($promo->discount_amount, 2) . ' to use this promo code.'
            );
        }

        $discount = self::calculateEstoreDiscount($promo, $eligibleSubtotal);

        return [
            'valid' => true,
            'message' => 'Promo code applied successfully.',
            'code' => $promo->code,
            'discount_amount' => $discount,
            'is_percentage' => (bool) $promo->is_percentage,
            'original_price' => $subtotal,
            'final_price' => max(0, $subtotal - $discount),
            'promo_code' => $promo,
        ];
    }

    /**
     * Absolute discount amount to subtract from the eligible subtotal.
     */
    public static function calculateEstoreDiscount(EstorePromoCode $promo, float $eligibleSubtotal): float
    {
        if ($promo->is_percentage) {
            return round($eligibleSubtotal * ($promo->discount_amount / 100), 2);
        }

        return round(min((float) $promo->discount_amount, $eligibleSubtotal), 2);
    }

    /**
     * Validate a Membership promo code against a specific tier.
     */
    public static function validateMembership(string $code, int $userId, int $tierId): array
    {
        $tier = MembershipTier::find($tierId);
        if (!$tier) {
            return self::failed('Invalid membership tier.');
        }

        $promo = MembershipPromoCode::where('code', $code)->first();
        if (!$promo || !$promo->canBeUsedByUser($userId) || !$promo->canBeAppliedToTier($tier->id)) {
            return self::failed('Invalid or expired promo code.');
        }

        $original = (float) $tier->cost;
        $discount = (float) $promo->calculateDiscount($original);

        return [
            'valid' => true,
            'message' => 'Promo code applied successfully.',
            'code' => $promo->code,
            'discount_amount' => round($discount, 2),
            'is_percentage' => (bool) $promo->is_percentage,
            'original_price' => $original,
            'final_price' => max(0, $original - $discount),
            'promo_code' => $promo,
        ];
    }

    private static function failed(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'code' => null,
            'discount_amount' => 0.0,
            'is_percentage' => false,
            'original_price' => 0.0,
            'final_price' => 0.0,
            'promo_code' => null,
        ];
    }
}
