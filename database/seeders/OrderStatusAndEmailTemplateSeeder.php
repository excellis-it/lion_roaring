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
                ['name' => 'Pending',    'slug' => 'pending',    'sort_order' => 1, 'is_active' => 1, 'is_pickup' => 0],
                ['name' => 'Processing', 'slug' => 'processing', 'sort_order' => 2, 'is_active' => 1, 'is_pickup' => 0],
                ['name' => 'Shipped',    'slug' => 'shipped',    'sort_order' => 3, 'is_active' => 1, 'is_pickup' => 0],
                ['name' => 'Out for Delivery', 'slug' => 'out_for_delivery', 'sort_order' => 4, 'is_active' => 1, 'is_pickup' => 0],
                ['name' => 'Delivered',  'slug' => 'delivered',  'sort_order' => 5, 'is_active' => 1, 'is_pickup' => 0],
                ['name' => 'Cancelled',  'slug' => 'cancelled',  'sort_order' => 6, 'is_active' => 1, 'is_pickup' => 0],

            ];

            $pickup_statuses = [
                ['name' => 'Pending',    'slug' => 'pickup_pending',    'sort_order' => 1, 'is_active' => 1, 'is_pickup' => 1],
                ['name' => 'Processing', 'slug' => 'pickup_processing', 'sort_order' => 2, 'is_active' => 1, 'is_pickup' => 1],
                ['name' => 'Ready For Pickup',    'slug' => 'pickup_ready_for_pickup',    'sort_order' => 3, 'is_active' => 1, 'is_pickup' => 1],
                ['name' => 'Picked Up', 'slug' => 'pickup_picked_up', 'sort_order' => 4, 'is_active' => 1, 'is_pickup' => 1],
                ['name' => 'Cancelled',  'slug' => 'pickup_cancelled',  'sort_order' => 5, 'is_active' => 1, 'is_pickup' => 1],

            ];

            foreach ($statuses as $s) {
                OrderStatus::updateOrCreate(
                    ['slug' => $s['slug']],
                    $s
                );
            }

            foreach ($pickup_statuses as $s) {
                OrderStatus::updateOrCreate(
                    ['slug' => $s['slug']],
                    $s
                );
            }

            // 2) Email templates (delivery)
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

<p>If you have any questions, contact us.</p>
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
                'out_for_delivery' => [
                    'title' => 'Out for Delivery',
                    'slug'  => 'order_out_for_delivery',
                    'subject' => 'Your order #{order_id} is out for delivery',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Your order <strong>#{order_id}</strong> is <strong>out for delivery</strong>.</p>

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

<p>If you have questions or want assistance, contact us.</p>
<p>Order summary:</p>
{order_list}
<p>Amount: {total_order_value}</p>
HTML
                ],
            ];

            // 3) Pickup email templates (pickup statuses)
            $pickupTemplates = [
                'pickup_pending' => [
                    'title' => 'Pickup Order Placed - Pending',
                    'slug'  => 'pickup_pending',
                    'subject' => 'Your pickup order #{order_id} has been received',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Thank you for your pickup order <strong>#{order_id}</strong>. Your order is currently <strong>Pending</strong>.</p>

<p><strong>Order details:</strong></p>
{order_list}

<p>If you have any questions, reply to this email: {customer_email}</p>
HTML
                ],
                'pickup_processing' => [
                    'title' => 'Pickup Order Processing',
                    'slug'  => 'pickup_processing',
                    'subject' => 'Your pickup order #{order_id} is being processed',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Good news — we are processing your pickup order <strong>#{order_id}</strong>.</p>

<p><strong>Order items:</strong></p>
{order_list}
HTML
                ],
                'pickup_ready_for_pickup' => [
                    'title' => 'Ready for Pickup',
                    'slug'  => 'pickup_ready_for_pickup',
                    'subject' => 'Your pickup order #{order_id} is ready for pickup',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Your pickup order <strong>#{order_id}</strong> is <strong>ready for pickup</strong>.</p>

<p>Order summary:</p>
{order_list}
HTML
                ],
                'pickup_picked_up' => [
                    'title' => 'Picked Up',
                    'slug'  => 'pickup_picked_up',
                    'subject' => 'Your pickup order #{order_id} has been picked up',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>Your pickup order <strong>#{order_id}</strong> has been <strong>picked up</strong>.</p>

<p>Thank you for shopping with us.</p>
HTML
                ],
                'pickup_cancelled' => [
                    'title' => 'Pickup Order Cancelled',
                    'slug'  => 'pickup_cancelled',
                    'subject' => 'Your pickup order #{order_id} has been cancelled',
                    'body' => <<<HTML
<p>Hi {customer_name},</p>
<p>We're sorry to inform you that your pickup order <strong>#{order_id}</strong> has been <strong>cancelled</strong>.</p>

<p>If you have questions or want assistance, contact us.</p>
HTML
                ],
            ];

            foreach ($templates as $statusSlug => $template) {
                // find the related order_status id (nullable if not found)
                $orderStatus = OrderStatus::where('slug', $statusSlug)->where('is_pickup', 0)->first();
                $orderStatusId = $orderStatus ? $orderStatus->id : null;

                // Delivery template
                OrderEmailTemplate::firstOrCreate(
                    ['slug' => $template['slug'], 'is_pickup' => false],
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

            foreach ($pickupTemplates as $statusSlug => $template) {
                $orderStatus = OrderStatus::where('slug', $statusSlug)->where('is_pickup', 1)->first();
                $orderStatusId = $orderStatus ? $orderStatus->id : null;

                OrderEmailTemplate::firstOrCreate(
                    ['slug' => $template['slug'], 'is_pickup' => true],
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
