<?php

namespace App\Services;

use App\Models\EstorePromoCode;
use Carbon\Carbon;

class PromoCodeService
{
    public static function validatePromoCode($code, $userId = null, $cartItems = [])
    {
        $promoCode = EstorePromoCode::where('code', $code)
            ->where('status', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->first();

        if (!$promoCode) {
            return [
                'valid' => false,
                'message' => 'Promo code not found or expired'
            ];
        }

        // Check scope validity
        $cartCollection = collect($cartItems);
        $cartTotal = $cartCollection->sum('subtotal');
        $eligibleSubtotal = $cartTotal;

        switch ($promoCode->scope_type) {
            case 'selected_users':
                if (!$userId || !in_array($userId, $promoCode->user_ids ?? [])) {
                    return [
                        'valid' => false,
                        'message' => 'This promo code is not applicable for your account'
                    ];
                }
                break;

            case 'selected_products':
                $cartProductIds = $cartCollection->pluck('product_id')->toArray();
                $applicableProducts = array_intersect($cartProductIds, $promoCode->product_ids ?? []);

                if (empty($applicableProducts)) {
                    return [
                        'valid' => false,
                        'message' => 'This promo code is not applicable for items in your cart'
                    ];
                }

                $eligibleSubtotal = $cartCollection
                    ->filter(fn($item) => in_array($item['product_id'], $applicableProducts))
                    ->sum('subtotal');
                break;
        }

        if (!$promoCode->is_percentage && $eligibleSubtotal < $promoCode->discount_amount) {
            return [
                'valid' => false,
                'message' => 'Cart total must be at least $' . number_format($promoCode->discount_amount, 2) . ' to use this promo code'
            ];
        }

        return [
            'valid' => true,
            'promo_code' => $promoCode,
            'message' => 'Promo code applied successfully'
        ];
    }

    public static function calculateDiscount($promoCode, $subtotal, $cartItems = [])
    {
        $discountAmount = 0;
        $applicableAmount = $subtotal;

        // For selected products, calculate discount only on applicable items
        if ($promoCode->scope_type === 'selected_products') {
            $applicableAmount = 0;
            $productIds = $promoCode->product_ids ?? [];

            foreach ($cartItems as $item) {
                if (in_array($item['product_id'], $productIds)) {
                    $applicableAmount += $item['subtotal'];
                }
            }
        }

        if ($promoCode->is_percentage) {
            $discountAmount = ($applicableAmount * $promoCode->discount_amount) / 100;
        } else {
            $discountAmount = min($promoCode->discount_amount, $applicableAmount);
        }

        return round($discountAmount, 2);
    }
}
