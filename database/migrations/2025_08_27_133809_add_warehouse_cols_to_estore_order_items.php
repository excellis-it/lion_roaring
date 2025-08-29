<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseColsToEstoreOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_order_items', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('warehouse_product_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('warehouse_product_id');
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
            $table->dropColumn('warehouse_product_id');
            $table->dropColumn('warehouse_id');
        });
    }
}
