<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMenuItems extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name')->nullable();
            $table->string('default_name')->nullable();
            $table->timestamps();
        });

        // Seed default menu items
        $defaults = [
            ['key' => 'messaging', 'default_name' => 'Messaging'],
            ['key' => 'chats', 'default_name' => 'Chats'],
            ['key' => 'team', 'default_name' => 'Team'],
            ['key' => 'mail', 'default_name' => 'Mail'],
            ['key' => 'education', 'default_name' => 'Education'],
            ['key' => 'topics', 'default_name' => 'Topics'],
            ['key' => 'becoming_sovereign', 'default_name' => 'Becoming Sovereign'],
            ['key' => 'becoming_christ_like', 'default_name' => 'Becoming Christ Like'],
            ['key' => 'becoming_leader', 'default_name' => 'Becoming a Leader'],
            ['key' => 'files', 'default_name' => 'Files'],
            ['key' => 'estore', 'default_name' => 'E-Store'],
            ['key' => 'elearning', 'default_name' => 'E-Learning'],
            ['key' => 'role_permission', 'default_name' => 'Role Permission'],
            ['key' => 'all_members', 'default_name' => 'All Members'],
            ['key' => 'user_activity', 'default_name' => 'User Activity'],
            ['key' => 'strategy', 'default_name' => 'Strategy'],
            ['key' => 'policy_guidance', 'default_name' => 'Policy & Guidance'],
            ['key' => 'bulletins', 'default_name' => 'Bulletins'],

            // Bulletins

            ['key' => 'bulletin_board', 'default_name' => 'Bulletins Board'],
            ['key' => 'create_bulletins', 'default_name' => 'Create Bulletins'],
            ['key' => 'job_posting', 'default_name' => 'Job Posting'],
            ['key' => 'meeting_schedule', 'default_name' => 'Meeting Schedule'],
            ['key' => 'live_events', 'default_name' => 'Live Events'],
            ['key' => 'private_collaboration', 'default_name' => 'Private Collaboration'],
            // Misc (for commented/other links)
            ['key' => 'activity', 'default_name' => 'Activity'],
            ['key' => 'calendar', 'default_name' => 'Calendar'],
            ['key' => 'calls', 'default_name' => 'Calls'],
            ['key' => 'communities_of_interest', 'default_name' => 'Communities of interest'],
            ['key' => 'warehouse_admins', 'default_name' => 'Warehouse Admins'],
            ['key' => 'ecclesias', 'default_name' => 'Ecclesias'],
            ['key' => 'help', 'default_name' => 'Help'],

            // E-store
            ['key' => 'e_store_dashboard', 'default_name' => 'E-store Dashboard'],
            ['key' => 'e_store_users', 'default_name' => 'E-store Users'],
            ['key' => 'product_categories', 'default_name' => 'Product Categories'],
            ['key' => 'manage_sizes', 'default_name' => 'Manage Sizes'],
            ['key' => 'manage_colors', 'default_name' => 'Manage Colors'],
            ['key' => 'promo_codes', 'default_name' => 'Promo Codes'],
            ['key' => 'e_store_settings', 'default_name' => 'E-store Settings'],
            ['key' => 'order_status', 'default_name' => 'Order Status'],
            ['key' => 'orders_email_templates', 'default_name' => 'Orders Email Templates'],
            ['key' => 'products', 'default_name' => 'Products'],
            ['key' => 'warehouses', 'default_name' => 'Warehouses'],
            ['key' => 'orders', 'default_name' => 'Orders'],

            // Warehouse Store
            ['key' => 'warehouse_store', 'default_name' => 'Warehouse Store'],
            ['key' => 'warehouse_products', 'default_name' => 'Warehouse Products'],
            ['key' => 'warehouse_orders', 'default_name' => 'Warehouse Orders'],

            // Elearning
            ['key' => 'e_learning_dashboard', 'default_name' => 'E-learning Dashboard'],
            ['key' => 'e_learning_categories', 'default_name' => 'E-learning Categories'],
            ['key' => 'e_learning_topics', 'default_name' => 'E-learning Topics'],
            ['key' => 'e_learning_products', 'default_name' => 'E-learning Products'],

            // User Activity (sub)
            ['key' => 'activity_dashboard', 'default_name' => 'Activity Dashboard'],
            ['key' => 'activity_list', 'default_name' => 'Activity List'],
        ];

        foreach ($defaults as $d) {
            DB::table('menu_items')->insert([
                'key' => $d['key'],
                'default_name' => $d['default_name'],
                'name' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
