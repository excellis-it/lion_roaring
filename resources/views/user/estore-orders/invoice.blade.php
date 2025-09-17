<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Delmarchio Eyewear Pvt. Ltd.</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/fontawesome.css" />
</head>

<body>
    <div
        style="font-family:'Roboto', sans-serif; font-size:12px; border:1px solid #cccccc; margin: 0 auto; width:720px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" valign="top" style="background:#7851a9;">&nbsp;</td>
            </tr>
            <tr>
                <td height="69" align="left" valign="middle" style="background:#f3f3f3;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="52%"> <img src="https://lionroaring.us/ecom_assets/images/estore_logo.png"
                                    style="height: 150px; width:150px; margin-left:20px;" />
                            </td>
                            <td width="48%">
                                <p style="margin: 0px 0px 8px;"><strong>LR PMA Ministries</strong>
                                    <br><span style="color:#666666;">SALES & COMMUNICATION OFFICE</span><br><span
                                        style="color:#666666;">{{ $order->warehouse_address ?? '' }}</span>
                                    <br><span style="color:#666666;">REGISTERED OFFICE</span><br><span
                                        style="color:#666666;">21030 Frederick Rd Suite G 452 (UPS Store), Germantown,
                                        Maryland 20876 (not for
                                        communication)</span>
                                </P>
                                <p style="margin: 0px;"><strong>Email:</strong> <span
                                        style="color:#666666;">info@lionroaring.com</span> | <strong> Phone:</strong>
                                    <span style="color:#666666;">+1 (240)-982-0054</span>
                                </P>
                                <p><strong>EIN :</strong> <span style="color:#666666;">33-6491742</span></p>
                                </p>
                            </td>
                        </tr>
                    </table>


                </td>
            </tr>

        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="36%" align="left" valign="top" style="padding-left:15px;">
                    <p
                        style="border-bottom:1px solid #cccccc; padding-bottom:10px;font-size: 16px;color: #7851a9;text-transform:uppercase;margin-bottom: 0px;">
                        BILL TO</p>
                    <p style="color: #313131"><span
                            style="font-size:18px;color: #313131"><strong>{{ $order->full_name ?? '' }}</strong></span><br />
                        {{ $order->email ?? '' }}<br />
                        {{ $order->phone ?? '' }}<br />
                        {{ $order->city ?? '' }}, {{ $order->state ?? '' }} - {{ $order->pincode ?? '' }}<br />
                        {{ $order->country ?? '' }}
                    </p>


                </td>
                <td width="33%" align="left" valign="top">&nbsp;</td>
                <td width="31%" align="left" valign="top">
                    <p
                        style="border-bottom:1px solid #cccccc; padding-bottom:10px;font-size: 16px;color: #7851a9;text-transform:uppercase;margin-bottom: 0px;">
                        INVOICE </p>
                    <p
                        style="border-bottom:1px solid #cccccc; padding-bottom:10px;color:#313131;font-size:13px;margin:8px 0px 0px;">
                        <span style="font-weight: 600;">Date:</span>
                        {{ $order->created_at ? $order->created_at->format('d M, Y') : '' }}

                    </p>

                    <p
                        style="border-bottom:1px solid #cccccc; padding-bottom:10px;color:#313131;font-size:13px;margin:8px 0px 0px;">
                        <span style="font-weight: 600;">Invoice No:</span>
                        {{ isset($order->order_number) && $order->order_number != '' ? stripslashes(trim($order->order_number)) : '' }}
                    </p>
                    <!-- <p style="padding-bottom:10px;color:#313131;font-size:13px;margin:8px 0px 0px;"><span style="font-weight: 600;">Due Date:</span> 15th August 2019</p> -->

                </td>
            </tr>
        </table>
        <h4 style="padding:0 0 10px 10px; margin: 0; font-size: 16px;">Product Details:</h4>
        <table width="100%" border="0" cellspacing="0" cellpadding="0"
            style="border-bottom:1px solid #cccccc; margin-top: 0;">
            <tr style="background:#7851a9">
                <td style="height:35px; color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;">Item Description
                </td>
                <td style="color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;text-align:center;">Color</td>
                <td style="color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;text-align:center;">Size</td>
                <td style="color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;text-align:center;">No. Of Items
                </td>
                <td style="color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;text-align:center;">Unit Price
                </td>
                <td style="color:#ffffff;height:36px;padding:0px 10px;font-size: 13px;text-align:center;">Net Price</td>
            </tr>
            @php
                $qty = 0;
            @endphp
            @foreach ($order->orderItems as $item)
                @php
                    $qty += $item->quantity;
                @endphp
                <tr>
                    <td style="height:35px;padding:0px 10px;color: #333; font-size: 12px;">
                        {{ $item->product_name ?? '' }} @if (isset($item->product) && $item->product->sku)
                            (SKU: {{ $item->product->sku ?? '' }})
                        @endif
                        <br />
                    </td>
                    <td style="padding:0px 10px;text-align:center;color: #333; font-size: 12px;">
                        {{ $item->color ?? '' }}</td>
                    <td style="padding:0px 10px;text-align:center;color: #333; font-size: 12px;">
                        {{ $item->size ?? '' }}</td>
                    <td style="padding:0px 10px;text-align:center;color: #333; font-size: 12px;">{{ $item->quantity }}
                    </td>
                    <td style="padding:0px 30px 0px 10px;text-align:right;color: #333; font-size: 12px;"> <i
                            class="fa fa-inr"></i> {{ sprintf('%01.2f', $item->price) }}</td>
                    @php
                        $net_price = $item->price * $item->quantity;
                    @endphp
                    <td style="padding:0px 30px 0px 10px;text-align:right;color: #333; font-size: 12px;"> <i
                            class="fa fa-inr"></i> {{ sprintf('%01.2f', $net_price) }}</td>
                </tr>
            @endforeach
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" valign="top" style="padding: 10px; width: 220px;">
                    <!-- <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr bgcolor="#7851a9">
                            <td height="35" style="padding:5px; color:#ffffff;border:1px solid #7851a9">Terms and Conditions:- </td>
                        </tr>
                        <tr>
                            <td height="45" style="padding:5px; color:#000000;border:1px solid #7851a9">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="6%" align="left" valign="top"><strong>1.</strong></td>
                                        <td width="94%" align="left" valign="top">Goods once sold cannot be taken back without any manufacturing defects.</td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top"><strong>2.</strong></td>
                                        <td align="left" valign="top">Interest @18% p.a. will be charged if the payment is not made within stipulated time (after 90 days of received goods)</td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top"><strong>3.</strong></td>
                                        <td align="left" valign="top">In case of any dispute, the matter will be resolved in Howrah Jurisdiction only.</td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top"><strong>4.</strong></td>
                                        <td align="left" valign="top">Please sell within MRP.</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table> -->
                    {{-- <img src="https://www.delmarchio.com/crm/public/uploads/scan_pay.jpg"> --}}
                </td>
                <td align="left" valign="top" style="width: 500px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="text-align:right;font-size:13px;font-weight:bold;color:#333; height:35px">Total
                                QTY</td>
                            <td
                                style="text-align: right;font-size:13px;border-bottom: 2px solid #eaeaea;color: #333; padding-right: 30px;">
                                {{ $qty }}</td>
                        </tr>
                        @if ($order->tax_amount > 0)
                            <tr>
                                <td style="text-align:right;font-size:13px;font-weight:bold;color:#333; height:35px">
                                    TAX:</td>
                                <td
                                    style="text-align: right;font-size:13px;border-bottom: 2px solid #eaeaea;color: #333; padding-right: 30px;">
                                    ${{ number_format($order->tax_amount, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if ($order->shipping_amount > 0)
                            <tr>
                                <td style="text-align:right;font-size:13px;font-weight:bold;color:#333; height:35px">
                                    SHIPPING</td>
                                <td
                                    style="text-align: right;font-size:13px;border-bottom: 2px solid #eaeaea;color: #333; padding-right: 30px;">
                                    $ {{ number_format($order->shipping_amount, 2) }}
                                </td>
                            </tr>
                        @endif
                        @if ($order->credit_card_fee > 0)
                            <tr>
                                <td style="text-align:right;font-size:13px;font-weight:bold;color:#333; height:35px">
                                    CREDIT CARD FEE:</td>
                                <td
                                    style="text-align: right;font-size:13px;border-bottom: 2px solid #eaeaea;color: #333; padding-right: 30px;">
                                    ${{ number_format($order->credit_card_fee, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="text-align:right;font-size:13px;font-weight:bold;color:#333; height:35px">
                                TOTAL AMOUNT</td>
                            <td
                                style="text-align: right;font-size:13px;border-bottom: 2px solid #eaeaea;color: #333; padding-right: 30px;">
                                $ {{ number_format($order->total_amount, 2) }}
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                {{-- <td height="40" style="padding:0px 20px 0 20px;" align="left" valign="top">
                    <p style="padding:10px 20px;"><img
                            src="https://www.delmarchio.com/crm/public/uploads/wl_logo.jpg"></p>
                </td>
                <td height="40" colspan="3" align="right"><img
                        src="https://www.delmarchio.com/images/signature.png" width="200" /></td> --}}
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" valign="top" style="border-bottom:1px solid #cccccc;">&nbsp;</td>
            </tr>

            <tr>
                <td align="center" valign="top" style="padding: 0px 10px 20px;">
                    <p style="font-size:13px;color: #666; text-align:center;">
                        <strong style="font-size: 13px;color: #000;">Note:</strong>
                    </p>
                    <p style="margin: 0px;font-size:11px;color: #666;">
                        If you have any question about this invoice, please contact +1 (240)-982-0054<br />
                        <strong>Thank You For Your Business! </strong>
                    </P>
                </td>
            </tr>
            <tr>
                <td align="left" valign="top" style="background:#7851a9">&nbsp;</td>
            </tr>
            <tr>
                <td align="left" valign="top" style="background:#333333">&nbsp;</td>
            </tr>
        </table>
    </div>


</body>

</html>
