<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;

class OrderEmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            'pending' => [
                'title' => 'Order Placed - Pending',
                'subject' => 'Your order #{order_id} has been received',
                'body' => "Hi {customer_name},\n\nYour order #{order_id} has been received.\n\nOrder summary:\n{order_list}\n\nAmount: {total_order_value}",
            ],
            'processing' => [
                'title' => 'Order Processing',
                'subject' => 'Your order #{order_id} is being processed',
                'body' => "Hi {customer_name},\n\nYour order #{order_id} is being processed.\n\nOrder summary:\n{order_list}\n\nAmount: {total_order_value}",
            ],
            'shipped' => [
                'title' => 'Order Shipped',
                'subject' => 'Your order #{order_id} has been shipped',
                'body' => "Hi {customer_name},\n\nYour order #{order_id} has been shipped.\n\nOrder summary:\n{order_list}\n\nArriving date: {arriving_date}\n\nAmount: {total_order_value}",
            ],
            'delivered' => [
                'title' => 'Order Delivered',
                'subject' => 'Your order #{order_id} has been delivered',
                'body' => "Hi {customer_name},\n\nYour order #{order_id} has been delivered.\n\nOrder summary:\n{order_list}\n\nAmount: {total_order_value}",
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'subject' => 'Your order #{order_id} has been cancelled',
                'body' => "Hi {customer_name},\n\nWe're sorry to inform you that your order #{order_id} has been cancelled.\n\nOrder summary:\n{order_list}\n\nAmount: {total_order_value}",
            ],
        ];

        foreach ($templates as $slug => $data) {
            $status = OrderStatus::where('slug', $slug)->first();
            if (!$status) continue;

            // Delivery template
            OrderEmailTemplate::firstOrCreate(
                [
                    'order_status_id' => $status->id,
                    'is_pickup' => false,
                ],
                [
                    'title' => $data['title'],
                    'slug' => 'order_' . $slug,
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                    'is_active' => true,
                ]
            );

            // Pickup template (slightly different copy)
            $pickupTitle = $data['title'] . ' (Pickup)';
            $pickupSubject = str_replace('Your order', 'Your pickup order', $data['subject']);
            $pickupBody = str_replace('Your order', 'Your pickup order', $data['body']);

            OrderEmailTemplate::firstOrCreate(
                [
                    'order_status_id' => $status->id,
                    'is_pickup' => true,
                ],
                [
                    'title' => $pickupTitle,
                    'slug' => 'pickup_' . $slug,
                    'subject' => $pickupSubject,
                    'body' => $pickupBody,
                    'is_active' => true,
                ]
            );
        }
    }
}
