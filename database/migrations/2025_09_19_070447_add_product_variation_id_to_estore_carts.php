<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductVariationIdToEstoreCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_carts', function (Blueprint $table) {
            if (!Schema::hasColumn('estore_carts', 'product_variation_id')) {
                $table->unsignedBigInteger('product_variation_id')->nullable()->after('warehouse_product_id');
                $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('cascade');
            }
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
            if (Schema::hasColumn('estore_carts', 'product_variation_id')) {
                $table->dropForeign(['product_variation_id']);
                $table->dropColumn('product_variation_id');
            }
        });
    }
}
