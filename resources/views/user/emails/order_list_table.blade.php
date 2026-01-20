<table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px; color:#333;">
    <thead>
        <tr>
            <th style="border:1px solid #ccc; padding:8px; background:#f3f3f3; text-align:left;">Product</th>
            <th style="border:1px solid #ccc; padding:8px; background:#f3f3f3; text-align:right;">Price</th>
            <th style="border:1px solid #ccc; padding:8px; background:#f3f3f3; text-align:right;">Qty</th>
            <th style="border:1px solid #ccc; padding:8px; background:#f3f3f3; text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->orderItems as $item)
            @php
                $price = $item->price ?? 0;
                $qty = $item->quantity ?? 0;
                $total = $price * $qty;
                $charges = $item->other_charges ? json_decode($item->other_charges, true) : [];
            @endphp
            <tr>
                <td style="border:1px solid #ccc; padding:8px;">
                    <strong>{{ $item->product_name ?? 'N/A' }}</strong>
                    @if (!empty($item->size))
                        <br>Size: {{ $item->size }}
                    @endif
                    @if (!empty($item->color))
                        <br>Color: {{ $item->color }}
                    @endif
                </td>
                <td style="border:1px solid #ccc; padding:8px; text-align:right;">${{ number_format($price, 2) }}</td>
                <td style="border:1px solid #ccc; padding:8px; text-align:right;">{{ $qty }}</td>
                <td style="border:1px solid #ccc; padding:8px; text-align:right;">${{ number_format($total, 2) }}</td>
            </tr>

            @if (!empty($charges))
                @foreach ($charges as $charge)
                    <tr style="background:#f5f5f5;">
                        <td colspan="3" style="border:1px solid #ccc; padding:8px 8px 8px 20px;">•
                            {{ $charge['charge_name'] ?? 'Other' }}</td>
                        <td style="border:1px solid #ccc; padding:8px; text-align:right;">
                            ${{ number_format($charge['charge_amount'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Subtotal:</th>
            <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                ${{ number_format($order->subtotal ?? 0, 2) }}</th>
        </tr>
        @if (!empty($order->promo_discount) && $order->promo_discount > 0)
            <tr>
                <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">
                    Promo Discount @if ($order->promo_code)
                        (Code: {{ $order->promo_code }})
                    @endif
                </th>
                <th style="border:1px solid #ccc; padding:8px; text-align:right;">-
                    ${{ number_format($order->promo_discount, 2) }}</th>
            </tr>
        @endif
        @if (!empty($order->tax_amount) && $order->tax_amount > 0)
            <tr>
                <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Tax:</th>
                <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                    ${{ number_format($order->tax_amount, 2) }}</th>
            </tr>
        @endif
        @if (!empty($order->shipping_amount) && $order->shipping_amount > 0)
            <tr>
                <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Shipping:</th>
                <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                    ${{ number_format($order->shipping_amount, 2) }}</th>
            </tr>
        @endif
        @if (!empty($order->handling_amount) && $order->handling_amount > 0)
            <tr>
                <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Handling:</th>
                <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                    ${{ number_format($order->handling_amount, 2) }}</th>
            </tr>
        @endif
        @if (!empty($order->credit_card_fee) && $order->credit_card_fee > 0)
            <tr>
                <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Credit Card Fee:</th>
                <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                    ${{ number_format($order->credit_card_fee, 2) }}</th>
            </tr>
        @endif
        <tr style="background:#e0f7ff;">
            <th colspan="3" style="border:1px solid #ccc; padding:8px; text-align:right;">Total:</th>
            <th style="border:1px solid #ccc; padding:8px; text-align:right;">
                ${{ number_format($order->total_amount ?? 0, 2) }}</th>
        </tr>
    </tfoot>
</table>
