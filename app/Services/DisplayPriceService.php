<?php

namespace App\Services;

use App\Models\EstoreCart;
use App\Models\EstorePromoCode;
use Illuminate\Support\Collection;

/**
 * Mirrors the webapp's `display_at` rules for ProductOtherCharge so the API returns the
 * same line-item breakdown that the web checkout page shows.
 *
 * Rules (from `app/Http/Controllers/Estore/ProductController.php`):
 *   - `display_at = 'listing'` charges are ALWAYS added to the item price.
 *   - `display_at = 'checkout'` charges are OPTIONAL — the user selects which to add
 *     at checkout (by charge name, per cart item).
 *   - Percentage charges are calculated off `price * quantity`, not off the running
 *     total (matches webapp behavior).
 */
class DisplayPriceService
{
    /**
     * @param Collection<int,EstoreCart> $cartItems     Cart items with `product.otherCharges` and `warehouseProduct` eager-loaded.
     * @param array<int,array<string>>   $selectedCheckoutCharges  Keyed by cart id, value is array of selected charge_name strings.
     * @param EstorePromoCode|null       $promo
     * @return array{
     *   items: array,
     *   subtotal: float,
     *   listing_charges_total: float,
     *   checkout_charges_total: float,
     *   promo_discount: float,
     *   total: float
     * }
     */
    public function calculateCart(Collection $cartItems, array $selectedCheckoutCharges = [], ?EstorePromoCode $promo = null): array
    {
        $items = [];
        $subtotal = 0.0;
        $listingTotal = 0.0;
        $checkoutTotal = 0.0;

        foreach ($cartItems as $cart) {
            $unitPrice = (float) ($cart->price ?? $cart->warehouseProduct?->price ?? 0);
            $qty = (int) ($cart->quantity ?? 1);
            $baseAmount = $unitPrice * $qty;

            $listingCharges = [];
            $listingSum = 0.0;
            $availableCheckoutCharges = [];
            $appliedCheckoutCharges = [];
            $appliedCheckoutSum = 0.0;

            $selectedNames = $selectedCheckoutCharges[$cart->id] ?? [];

            foreach ($cart->product?->otherCharges ?? [] as $charge) {
                $amount = $charge->charge_type === 'percentage'
                    ? round($baseAmount * ((float) $charge->charge_amount / 100), 2)
                    : round((float) $charge->charge_amount, 2);

                $displayAt = $charge->display_at ?? 'listing';
                $row = [
                    'charge_name' => $charge->charge_name,
                    'charge_type' => $charge->charge_type,
                    'charge_amount' => (float) $charge->charge_amount,
                    'calculated_amount' => $amount,
                    'display_at' => $displayAt,
                ];

                if ($displayAt === 'checkout') {
                    $availableCheckoutCharges[] = $row;
                    if (in_array($charge->charge_name, $selectedNames, true)) {
                        $appliedCheckoutCharges[] = $row;
                        $appliedCheckoutSum += $amount;
                    }
                } else {
                    $listingCharges[] = $row;
                    $listingSum += $amount;
                }
            }

            $itemSubtotal = round($baseAmount + $listingSum + $appliedCheckoutSum, 2);

            $items[] = [
                'cart_id' => $cart->id,
                'product_id' => $cart->product_id,
                'product_name' => $cart->product?->name,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'base_amount' => round($baseAmount, 2),
                'listing_charges' => $listingCharges,
                'listing_charges_total' => round($listingSum, 2),
                'available_checkout_charges' => $availableCheckoutCharges,
                'applied_checkout_charges' => $appliedCheckoutCharges,
                'applied_checkout_charges_total' => round($appliedCheckoutSum, 2),
                'item_subtotal' => $itemSubtotal,
            ];

            $subtotal += $baseAmount;
            $listingTotal += $listingSum;
            $checkoutTotal += $appliedCheckoutSum;
        }

        $preDiscountTotal = round($subtotal + $listingTotal + $checkoutTotal, 2);

        $promoDiscount = 0.0;
        if ($promo) {
            $promoDiscount = PromoCodeValidator::calculateEstoreDiscount($promo, $preDiscountTotal);
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'listing_charges_total' => round($listingTotal, 2),
            'checkout_charges_total' => round($checkoutTotal, 2),
            'promo_discount' => round($promoDiscount, 2),
            'total' => max(0.0, round($preDiscountTotal - $promoDiscount, 2)),
        ];
    }
}
