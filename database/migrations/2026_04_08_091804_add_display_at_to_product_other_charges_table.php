<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayAtToProductOtherChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_other_charges', function (Blueprint $table) {
            $table->enum('display_at', ['listing', 'checkout'])->default('listing')->after('charge_type');
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
            $table->dropColumn('display_at');
        });
    }
}
