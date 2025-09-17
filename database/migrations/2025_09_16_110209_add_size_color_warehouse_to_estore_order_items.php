<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColorWarehouseToEstoreOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_order_items', function (Blueprint $table) {
             $table->string('size')->nullable()->after('color_id');
            $table->string('color')->nullable()->after('size');
            $table->string('warehouse_name')->nullable()->after('color');
            // warehouse address
            $table->string('warehouse_address')->nullable()->after('warehouse_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_order_items', function (Blueprint $table) {
              $table->dropColumn(['size', 'color', 'warehouse_name', 'warehouse_address']);
        });
    }
}
