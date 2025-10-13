<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OrderStatus;
use App\Models\OrderEmailTemplate;

class OrderStatusAndEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) Order statuses
            $statuses = [
                ['name' => 'Pending',    'slug' => 'pending',    'color' => '#FFA500', 'sort_order' => 1, 'is_active' => 1],
                ['name' => 'Processing', 'slug' => 'processing', 'color' => '#00BFFF', 'sort_order' => 2, 'is_active' => 1],
                ['name' => 'Shipped',    'slug' => 'shipped',    'color' => '#32CD32', 'sort_order' => 3, 'is_active' => 1],
                ['name' => 'Delivered',  'slug' => 'delivered',  'color' => '#008000', 'sort_order' => 4, 'is_active' => 1],
                ['name' => 'Cancelled',  'slug' => 'cancelled',  'color' => '#FF0000', 'sort_order' => 5, 'is_active' => 1],
            ];

            foreach ($statuses as $s) {
                OrderStatus::updateOrCreate(
                    ['slug' => $s['slug']],
                    $s
                );
            }

            // 2) Email templates (one per status — optional)
            $templates = [
                'pending' => [
                    'title' => 'Order Placed - Pending',
                    'slug'  => 'order_pending',
                    'subject' => 'Your order #{order_id} has been received',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Thank you for your order <strong>#{order_id}</strong>. Your order is currently <strong>Pending</strong>.</p>

<p><strong>Order details:</strong></p>
{order_list}

<p>Estimated arriving date: {arriving_date}</p>
<p>Total: {total_order_value}</p>

<p>If you have any questions, reply to this email: {customer_email}</p>
HTML
                ],
                'processing' => [
                    'title' => 'Order Processing',
                    'slug'  => 'order_processing',
                    'subject' => 'Your order #{order_id} is being processed',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Good news — we are processing your order <strong>#{order_id}</strong>.</p>

<p><strong>Order items:</strong></p>
{order_list}

<p>Expected delivery / arriving date: {arriving_date}</p>
<p>Order total: {total_order_value}</p>
HTML
                ],
                'shipped' => [
                    'title' => 'Order Shipped',
                    'slug'  => 'order_shipped',
                    'subject' => 'Your order #{order_id} has been shipped',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Your order <strong>#{order_id}</strong> has been <strong>shipped</strong>.</p>

<p>Items:</p>
{order_list}

<p>Arriving date: {arriving_date}</p>
<p>Order total: {total_order_value}</p>
HTML
                ],
                'delivered' => [
                    'title' => 'Order Delivered',
                    'slug'  => 'order_delivered',
                    'subject' => 'Your order #{order_id} has been delivered',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>We have delivered your order <strong>#{order_id}</strong>. We hope you enjoy your purchase!</p>

<p>Order summary:</p>
{order_list}

<p>Total paid: {total_order_value}</p>
<p>If there are any issues contact us: {customer_email}</p>
HTML
                ],
                'cancelled' => [
                    'title' => 'Order Cancelled',
                    'slug'  => 'order_cancelled',
                    'subject' => 'Your order #{order_id} has been cancelled',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>We're sorry to inform you that your order <strong>#{order_id}</strong> has been <strong>cancelled</strong>.</p>

<p>If you have questions or want assistance, reply to: {customer_email}</p>
<p>Order summary:</p>
{order_list}
<p>Amount: {total_order_value}</p>
HTML
                ],
            ];

            foreach ($templates as $statusSlug => $template) {
                // find the related order_status id (nullable if not found)
                $orderStatus = OrderStatus::where('slug', $statusSlug)->first();
                $orderStatusId = $orderStatus ? $orderStatus->id : null;

                OrderEmailTemplate::updateOrCreate(
                    ['slug' => $template['slug']],
                    [
                        'title' => $template['title'],
                        'slug' => $template['slug'],
                        'order_status_id' => $orderStatusId,
                        'subject' => $template['subject'],
                        'body' => $template['body'],
                        'is_active' => 1,
                    ]
                );
            }
        });
    }
}
