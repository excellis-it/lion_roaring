<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\EstoreCart;
use App\Models\EstoreSetting;
use App\Models\User;
use App\Models\WareHouse;
use App\Services\PromoCodeService;
use App\Services\PromoCodeValidator;
use Illuminate\Support\Collection;

/**
 * Builds cart/checkout totals for the mobile API using the same rules as
 * {@see \App\Http\Controllers\Estore\ProductController::cart()} and {@see checkout()}.
 */
class EstoreCartSummaryService
{
    public function summarizeForCartPage(?User $user, ?string $promoCode = null): array
    {
        $carts = $this->loadCarts($user);
        $estoreSettings = EstoreSetting::first();
        $hasChanges = false;
        $items = [];
        $itemsTotal = 0.0;
        $totalQuantity = 0;

        foreach ($carts as $cart) {
            $line = $this->buildCartLine($cart, $estoreSettings, $hasChanges);
            $hasChanges = $hasChanges
                || ($line['meta']['price_changed'] ?? false)
                || ($line['meta']['out_of_stock'] ?? false);
            if (! ($line['meta']['out_of_stock'] ?? false)) {
                $itemsTotal += $line['subtotal'];
                $totalQuantity += (int) $line['quantity'];
            }
            $items[] = $line;
        }

        $promo = $this->resolvePromo($promoCode, $user?->id, $items, $itemsTotal);
        $shipping = $this->shippingForQuantity($estoreSettings, $totalQuantity, false);

        $totalPayable = max(0, round(
            $itemsTotal - $promo['discount'] + $shipping['shipping'] + $shipping['handling'],
            2
        ));

        return [
            'items' => $items,
            'has_changes' => $hasChanges,
            'items_total' => round($itemsTotal, 2),
            'total_quantity' => $totalQuantity,
            'promo_code' => $promo['code'],
            'promo_discount' => $promo['discount'],
            'shipping_amount' => $shipping['shipping'],
            'handling_amount' => $shipping['handling'],
            'tax_amount' => 0.0,
            'credit_card_fee' => 0.0,
            'total_payable' => $totalPayable,
            'settings' => $this->settingsPayload($estoreSettings),
            'nearest_warehouse' => $this->nearestWarehousePayload($user),
        ];
    }

    /**
     * @param  array<int, array<string>>  $selectedCheckoutCharges
     */
    public function summarizeForCheckout(
        ?User $user,
        ?string $promoCode,
        int $orderMethod = 0,
        string $paymentType = 'debit',
        array $selectedCheckoutCharges = []
    ): array {
        $carts = $this->loadCarts($user);
        if ($carts->isEmpty()) {
            return ['empty' => true];
        }

        $estoreSettings = EstoreSetting::first();
        $isPickup = $orderMethod === 1 && ($estoreSettings?->is_pickup_available ?? false);

        $breakdown = (new DisplayPriceService())->calculateCart(
            $carts,
            $selectedCheckoutCharges,
            $promoCode ? $this->promoModel($promoCode, $user?->id, $carts) : null
        );

        $totalQuantity = (int) $carts->sum('quantity');
        $preTaxSubtotal = round(
            ($breakdown['subtotal'] ?? 0)
            + ($breakdown['listing_charges_total'] ?? 0)
            + ($breakdown['checkout_charges_total'] ?? 0)
            - ($breakdown['promo_discount'] ?? 0),
            2
        );

        $shipping = $this->shippingForQuantity($estoreSettings, $totalQuantity, $isPickup);
        $taxAmount = 0.0;
        if ($estoreSettings && ($estoreSettings->tax_percentage ?? 0) > 0) {
            $taxAmount = round($preTaxSubtotal * ((float) $estoreSettings->tax_percentage / 100), 2);
        }

        $totalBeforeCc = round($preTaxSubtotal + $shipping['shipping'] + $shipping['handling'] + $taxAmount, 2);
        $ccPercent = (float) ($estoreSettings->credit_card_percentage ?? 0);
        $creditCardFee = ($paymentType === 'credit' && $ccPercent > 0)
            ? round($totalBeforeCc * ($ccPercent / 100), 2)
            : 0.0;

        $cartLines = [];
        foreach ($carts as $cart) {
            $hasChanges = false;
            $cartLines[] = $this->buildCartLine($cart, $estoreSettings, $hasChanges);
        }

        return array_merge($breakdown, [
            'cart_lines' => $cartLines,
            'order_method' => $isPickup ? 1 : 0,
            'is_pickup' => $isPickup,
            'shipping_amount' => $shipping['shipping'],
            'handling_amount' => $shipping['handling'],
            'tax_amount' => $taxAmount,
            'credit_card_percentage' => $ccPercent,
            'credit_card_fee' => $creditCardFee,
            'total' => round($totalBeforeCc + $creditCardFee, 2),
            'total_quantity' => $totalQuantity,
            'settings' => $this->settingsPayload($estoreSettings),
            'nearest_warehouse' => $this->nearestWarehousePayload($user),
        ]);
    }

    protected function loadCarts(?User $user): Collection
    {
        $query = EstoreCart::with(['product.otherCharges', 'warehouseProduct', 'size', 'color']);
        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->get();
    }

    protected function buildCartLine(EstoreCart $cart, ?EstoreSetting $estoreSettings, bool &$hasChanges): array
    {
        $warehouseProduct = $cart->warehouseProduct;
        $currentWarehousePrice = (float) ($warehouseProduct?->price ?? 0);
        $availableQty = (int) ($warehouseProduct?->quantity ?? 0);

        $meta = [
            'price_changed' => false,
            'out_of_stock' => false,
            'original_price' => (float) ($cart->price ?? 0),
            'current_price' => $currentWarehousePrice,
        ];

        if (($cart->price ?? 0) != $currentWarehousePrice && ! ($cart->product?->is_free)) {
            $cart->old_price = $cart->price;
            $cart->price = $currentWarehousePrice;
            $cart->save();
            $meta['price_changed'] = true;
            $meta['original_price'] = (float) $cart->old_price;
            $hasChanges = true;
        }

        if ($availableQty <= 0) {
            $meta['out_of_stock'] = true;
            $hasChanges = true;
        } elseif ($cart->quantity > $availableQty) {
            $cart->quantity = $availableQty;
            $cart->save();
            $hasChanges = true;
        }

        $listingCharges = 0.0;
        $checkoutCharges = 0.0;
        $checkoutChargeRows = [];
        $unitPrice = (float) ($cart->price ?? $currentWarehousePrice);

        foreach ($cart->product?->otherCharges ?? [] as $charge) {
            $displayAt = $charge->display_at ?? 'listing';
            $chargeVal = $charge->charge_type === 'percentage'
                ? $unitPrice * $cart->quantity * ((float) $charge->charge_amount / 100)
                : (float) $charge->charge_amount;

            if ($displayAt === 'checkout') {
                $checkoutCharges += $chargeVal;
                $checkoutChargeRows[] = [
                    'charge_name' => $charge->charge_name,
                    'charge_type' => $charge->charge_type,
                    'charge_amount' => (float) $charge->charge_amount,
                    'calculated_amount' => round($chargeVal, 2),
                    'display_at' => 'checkout',
                ];
            } else {
                $listingCharges += $chargeVal;
            }
        }

        $subtotal = $meta['out_of_stock']
            ? 0.0
            : round(($unitPrice * $cart->quantity) + $listingCharges + $checkoutCharges, 2);

        $maxOrderQty = $estoreSettings->max_order_quantity ?? null;
        $warehouseMax = $availableQty;
        $inputMax = $warehouseMax;
        if ($maxOrderQty && $maxOrderQty > 0) {
            $inputMax = min($warehouseMax, $maxOrderQty);
        }

        $imagePath = $cart->product?->getProductFirstImage($cart->color_id);

        return [
            'id' => $cart->id,
            'product_id' => $cart->product_id,
            'product_name' => $cart->product?->name ?? '',
            'sku' => $warehouseProduct?->sku ?? '',
            'product_image' => $imagePath ? Helper::publicStorageUrl($imagePath) : null,
            'unit_price' => $unitPrice,
            'quantity' => (int) $cart->quantity,
            'max_quantity' => $inputMax,
            'size_name' => $cart->size?->size ?? null,
            'color_name' => $cart->color?->color_name ?? null,
            'listing_charges' => round($listingCharges, 2),
            'checkout_charges' => round($checkoutCharges, 2),
            'checkout_charge_rows' => $checkoutChargeRows,
            'subtotal' => $subtotal,
            'meta' => $meta,
        ];
    }

    protected function resolvePromo(?string $code, ?int $userId, array $items, float $itemsTotal): array
    {
        if (! $code || $itemsTotal <= 0) {
            return ['code' => null, 'discount' => 0.0];
        }

        $cartItems = collect($items)
            ->filter(fn ($i) => ! ($i['meta']['out_of_stock'] ?? false))
            ->map(fn ($i) => ['product_id' => $i['product_id'], 'subtotal' => $i['subtotal']])
            ->values()
            ->toArray();

        $validation = PromoCodeService::validatePromoCode($code, $userId, $cartItems);
        if (! ($validation['valid'] ?? false)) {
            return ['code' => null, 'discount' => 0.0, 'error' => $validation['message'] ?? 'Invalid promo code'];
        }

        $discount = PromoCodeService::calculateDiscount($validation['promo_code'], $itemsTotal, $cartItems);

        return ['code' => $code, 'discount' => round((float) $discount, 2)];
    }

    protected function promoModel(?string $code, ?int $userId, Collection $carts)
    {
        if (! $code) {
            return null;
        }
        $items = $carts->map(fn ($c) => [
            'product_id' => $c->product_id,
            'subtotal' => (float) ($c->price ?? 0) * (int) ($c->quantity ?? 1),
        ])->toArray();
        $result = PromoCodeValidator::validateEstore($code, $userId, $items);

        return $result['valid'] ? $result['promo_code'] : null;
    }

    protected function shippingForQuantity(?EstoreSetting $settings, int $qty, bool $isPickup): array
    {
        if ($isPickup || ! $settings) {
            return ['shipping' => 0.0, 'handling' => 0.0];
        }

        if (is_array($settings->shipping_rules) && count($settings->shipping_rules) > 0) {
            $row = $settings->getShippingForQuantity($qty);

            return [
                'shipping' => round((float) ($row['shipping_cost'] ?? 0), 2),
                'handling' => round((float) ($row['delivery_cost'] ?? 0), 2),
            ];
        }

        return [
            'shipping' => round((float) ($settings->shipping_cost ?? 0), 2),
            'handling' => round((float) ($settings->delivery_cost ?? 0), 2),
        ];
    }

    protected function settingsPayload(?EstoreSetting $settings): array
    {
        if (! $settings) {
            return [];
        }

        return [
            'is_pickup_available' => (bool) $settings->is_pickup_available,
            'max_order_quantity' => $settings->max_order_quantity,
            'tax_percentage' => (float) ($settings->tax_percentage ?? 0),
            'credit_card_percentage' => (float) ($settings->credit_card_percentage ?? 0),
            'shipping_rules' => $settings->shipping_rules ?? [],
            'shipping_cost' => (float) ($settings->shipping_cost ?? 0),
            'delivery_cost' => (float) ($settings->delivery_cost ?? 0),
            'cancel_within_hours' => $settings->cancel_within_hours ?? 24,
        ];
    }

    protected function nearestWarehousePayload(?User $user): ?array
    {
        $lat = $user?->location_lat;
        $lng = $user?->location_lng;
        $nearest = null;
        $distance = null;

        if ($lat && $lng) {
            $data = Helper::getNearestWarehouse($lat, $lng);
            $nearest = $data['warehouse'] ?? null;
            $distance = $data['distance_km'] ?? null;
        }

        if (! $nearest) {
            $nearest = WareHouse::where('is_active', 1)->whereHas('warehouseProducts')->with('country')->first();
        }

        if (! $nearest) {
            return null;
        }

        return [
            'id' => $nearest->id,
            'name' => $nearest->name,
            'address' => $nearest->address,
            'country' => $nearest->country?->name,
            'distance_km' => $distance,
            'latitude' => $nearest->location_lat,
            'longitude' => $nearest->location_lng,
        ];
    }
}
