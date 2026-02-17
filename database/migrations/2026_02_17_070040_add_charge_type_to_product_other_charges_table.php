<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChargeTypeToProductOtherChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_other_charges', function (Blueprint $table) {
            $table->string('charge_type')->default('fixed')->after('charge_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_other_charges', function (Blueprint $table) {
            $table->dropColumn('charge_type');
        });
    }
}
