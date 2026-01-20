<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHandlingAmountToEstoreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            $table->decimal('handling_amount', 10, 2)->default(0)->after('shipping_amount');
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
            $table->dropColumn('handling_amount');
        });
    }
}
