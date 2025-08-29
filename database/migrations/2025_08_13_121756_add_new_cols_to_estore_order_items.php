<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColsToEstoreOrderItems extends Migration
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
            // add size id, color id and other charges
            $table->unsignedBigInteger('size_id')->nullable()->after('quantity');
            $table->unsignedBigInteger('color_id')->nullable()->after('size_id');
            $table->json('other_charges')->nullable()->after('color_id');
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
            //
            $table->dropColumn('size_id');
            $table->dropColumn('color_id');
            $table->dropColumn('other_charges');
        });
    }
}
