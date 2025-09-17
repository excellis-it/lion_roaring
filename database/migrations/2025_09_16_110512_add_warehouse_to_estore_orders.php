<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseToEstoreOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
             $table->string('warehouse_name')->nullable()->after('warehouse_id');
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
        Schema::table('estore_orders', function (Blueprint $table) {
                $table->dropColumn('warehouse_name');
                $table->dropColumn('warehouse_address');
        });
    }
}
