<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColsToEstoreCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_carts', function (Blueprint $table) {
            //
            // Add new columns for size and color
            $table->unsignedBigInteger('size_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('color_id')->nullable()->after('size_id');
            // remove price
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_carts', function (Blueprint $table) {
            //
            $table->dropColumn('size_id');
            $table->dropColumn('color_id');
            $table->decimal('price', 10, 2)->after('product_id');
        });
    }
}
