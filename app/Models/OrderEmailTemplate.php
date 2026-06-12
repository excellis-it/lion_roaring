<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEmailTemplate extends BaseModel
{
    use HasFactory;

    protected $table = 'order_email_templates';

    protected $fillable = [
        'title',
        'slug',
        'order_status_id',
        'is_pickup',
        'subject',
        'body',
        'is_active',
        'sort_order',
    ];

    /**
     * Optional: relation to OrderStatus
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public static function availablePlaceholders(): array
    {
        return [
            '{customer_name}',
            '{customer_email}',
            '{order_list}',
            '{order_id}',
            '{arriving_date}',
            '{total_order_value}',
            '{order_details_url_button}',
            '{order_note}',
            '{warehouse_admin_mail}',
        ];
    }

    /**
     * @param  array{
     *     order_list?: string,
     *     order_details_button_html?: string,
     *     order_details_suffix?: string,
     *     include_order_details_button?: bool,
     * }  $options
     */
    public static function replacePlaceholders(string $content, EstoreOrder $order, array $options = []): string
    {
        $order->loadMissing(['warehouse.admins', 'orderItems.warehouse.admins']);

        $orderList = $options['order_list']
            ?? view('user.emails.order_list_table', ['order' => $order])->render();

        if (!empty($options['order_details_button_html'])) {
            $orderDetailsUrlButton = $options['order_details_button_html'];
        } elseif ($options['include_order_details_button'] ?? true) {
            $orderDetailsUrl = route('e-store.order-details', $order->id);
            $orderDetailsUrlButton = '<a href="' . $orderDetailsUrl . '" style="
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #643271;
                    text-decoration: none;
                    border-radius: 5px;
                ">View Order Details</a>';
            $orderDetailsUrlButton .= $options['order_details_suffix'] ?? '';
        } else {
            $orderDetailsUrlButton = '';
        }

        $replacements = [
            '{customer_name}' => trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')),
            '{customer_email}' => $order->email ?? '',
            '{order_list}' => $orderList,
            '{order_id}' => $order->order_number ?? '',
            '{arriving_date}' => $order->expected_delivery_date
                ? Carbon::parse($order->expected_delivery_date)->format('M d, Y')
                : '',
            '{total_order_value}' => number_format($order->total_amount ?? 0, 2),
            '{order_details_url_button}' => $orderDetailsUrlButton,
            '{order_note}' => $order->notes ?? '',
            '{warehouse_admin_mail}' => static::resolveWarehouseAdminMail($order),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    protected static function resolveWarehouseAdminMail(EstoreOrder $order): string
    {
        $emails = collect();
        $warehouses = collect();

        if ($order->warehouse) {
            $warehouses->push($order->warehouse);
        }

        foreach ($order->orderItems as $item) {
            if ($item->warehouse) {
                $warehouses->push($item->warehouse);
            }
        }

        foreach ($warehouses->unique('id') as $warehouse) {
            if (!empty($warehouse->contact_us_mail)) {
                $emails->push($warehouse->contact_us_mail);
                continue;
            }

            $emails = $emails->merge($warehouse->admins->pluck('email'));
        }

        return $emails->filter()->unique()->values()->implode(', ');
    }
}
