<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Notification</title>
</head>
@php
    use App\Helpers\Helper;
@endphp
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin:0; padding:0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">

                    {{-- Header with Logo --}}
                    <tr>
                        <td align="center" style="padding:20px; background-color:#7851a9;">
                            <img src="{{ Helper::estoreHeaderLogoUrl() }}" alt="{{ config('app.name') }}" style="max-height:60px;">
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding:20px;">
                            <h3 style="margin:0; color:#333;">Hello {{ $user->full_name ?? '' }},</h3>
                            <p style="color:#555; font-size:14px;">A new order <strong>#{{ $order->order_number }}</strong> has been placed with <strong>{{ count($warehouseCarts ?: $order->carts) }} item(s)</strong>.</p>
                        </td>
                    </tr>

                    {{-- Order Items Table --}}
                    <tr>
                        <td style="padding:0 20px 20px 20px;">
                            <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse; border:1px solid #ccc; font-size:13px;">
                                <thead style="background-color:#7851a9; color:#fff;">
                                    <tr>
                                        <th align="left">Item Description</th>
                                        <th align="center">Color</th>
                                        <th align="center">Size</th>
                                        <th align="center">No. Of Items</th>
                                        <th align="right">Unit Price</th>
                                        <th align="right">Net Price</th>
                                    </tr>
                                </thead>
                                <tbody style="color:#333; font-size:12px;">
                                    @php $qty = 0; $subtotal = 0; @endphp
                                    @foreach ($order->orderItems as $item)
                                        @php
                                            $price = $item->price ?? 0;
                                            $quantity = $item->quantity ?? 0;
                                            $net_price = $price * $quantity;
                                            $qty += $quantity;
                                            $charges = $item->other_charges ? json_decode($item->other_charges, true) : [];
                                            $subtotal += $item->total;
                                        @endphp
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td>{{ $item->product_name ?? '' }}<br>
                                                @if(isset($item->product) && $item->product->sku)
                                                    <small style="color:#888;">SKU: {{ $item->product->sku ?? '' }}</small>
                                                @endif
                                            </td>
                                            <td align="center">{{ $item->color ?? '' }}</td>
                                            <td align="center">{{ $item->size ?? '' }}</td>
                                            <td align="center">{{ $quantity }}</td>
                                            <td align="right">${{ number_format($price, 2) }}</td>
                                            <td align="right">${{ number_format($net_price, 2) }}</td>
                                        </tr>

                                        {{-- Other Charges --}}
                                        @if(!empty($charges))
                                            @foreach($charges as $charge)
                                                <tr style="background:#f9f9f9; font-size:12px; color:#555;">
                                                    <td style="padding-left:15px;">• {{ $charge['charge_name'] ?? 'Other' }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td align="right">${{ number_format($charge['charge_amount'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- Summary Table --}}
                    <tr>
                        <td style="padding:0 20px 20px 20px;">
                            <table width="100%" cellpadding="5" cellspacing="0" style="font-size:13px;">
                                <tr>
                                    <td style="width:60%;"></td>
                                    <td style="width:40%;">
                                        <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="right" style="font-weight:bold;">SUBTOTAL:</td>
                                                <td align="right">${{ number_format($subtotal, 2) }}</td>
                                            </tr>

                                            @if(!empty($order->promo_discount) && $order->promo_discount > 0)
                                                <tr>
                                                    <td align="right" style="font-weight:bold;">PROMO DISCOUNT:</td>
                                                    <td align="right">- ${{ number_format($order->promo_discount, 2) }}</td>
                                                </tr>
                                            @endif

                                            @if(!empty($order->tax_amount) && $order->tax_amount > 0)
                                                <tr>
                                                    <td align="right" style="font-weight:bold;">TAX:</td>
                                                    <td align="right">${{ number_format($order->tax_amount, 2) }}</td>
                                                </tr>
                                            @endif

                                            @if(!empty($order->shipping_amount) && $order->shipping_amount > 0)
                                                <tr>
                                                    <td align="right" style="font-weight:bold;">SHIPPING:</td>
                                                    <td align="right">${{ number_format($order->shipping_amount, 2) }}</td>
                                                </tr>
                                            @endif

                                            @if(!empty($order->credit_card_fee) && $order->credit_card_fee > 0)
                                                <tr>
                                                    <td align="right" style="font-weight:bold;">CREDIT CARD FEE:</td>
                                                    <td align="right">${{ number_format($order->credit_card_fee, 2) }}</td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td align="right" style="font-weight:bold;">TOTAL AMOUNT:</td>
                                                <td align="right">${{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px; background:#f4f4f4; text-align:center; font-size:12px; color:#888;">
                            Thank you for choosing {{ config('app.name') }}.<br>
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
