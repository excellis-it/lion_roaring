<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderEmailTemplate;

class OrderEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderEmailTemplate::updateOrCreate(
            ['slug' => 'digital'],
            [
                'title' => 'Digital Product Email',
                'subject' => 'Your order is paid',
                'body' => '<p>Hello {customer_name},</p><p>Thank you for your order. Your payment has been successfully received. You can now download the product and view the full details in your order history.</p><p><strong>Order Number:</strong> {order_id}</p>{order_list}<p><strong>Total Amount:</strong> ${total_order_value}</p>{order_details_url_button}',
                'is_active' => 1,
                'is_pickup' => 0,
                'sort_order' => 0,
            ]
        );
    }
}
