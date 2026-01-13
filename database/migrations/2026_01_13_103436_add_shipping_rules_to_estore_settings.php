<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingRulesToEstoreSettings extends Migration
{
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('estore_settings', 'shipping_rules')) {
                $table->json('shipping_rules')->nullable()->after('delivery_cost');
            }
        });

        // Optional: seed a sensible default if no row exists
        if (Schema::hasTable('estore_settings')) {
            try {
                $setting = \App\Models\EstoreSetting::first();
                if ($setting && is_null($setting->shipping_rules)) {
                    $setting->shipping_rules = [
                        ['min_qty' => 0, 'max_qty' => 4, 'shipping_cost' => 5, 'delivery_cost' => 2],
                        ['min_qty' => 5, 'max_qty' => 19, 'shipping_cost' => 3, 'delivery_cost' => 1],
                        ['min_qty' => 20, 'max_qty' => null, 'shipping_cost' => 0, 'delivery_cost' => 0],
                    ];
                    $setting->save();
                }
            } catch (\Throwable $e) {
                // ignore in migration if model not available yet
            }
        }
    }

    public function down()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            if (Schema::hasColumn('estore_settings', 'shipping_rules')) {
                $table->dropColumn('shipping_rules');
            }
        });
    }
}
