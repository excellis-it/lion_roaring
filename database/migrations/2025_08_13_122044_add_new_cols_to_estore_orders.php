<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColsToEstoreOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            // add is pickup
            $table->boolean('is_pickup')->default(false)->after('order_number');
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
            //
            $table->dropColumn('is_pickup');
        });
    }
}
