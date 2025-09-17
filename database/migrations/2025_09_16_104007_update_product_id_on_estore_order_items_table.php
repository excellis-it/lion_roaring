<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductIdOnEstoreOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_order_items', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['product_id']);
        });

        Schema::table('estore_order_items', function (Blueprint $table) {
            // Drop the column and re-add it as nullable
            $table->dropColumn('product_id');
        });

        Schema::table('estore_order_items', function (Blueprint $table) {
            // Recreate product_id as nullable with foreign key SET NULL
            $table->unsignedBigInteger('product_id')->nullable()->after('order_id');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('estore_order_items', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['product_id']);
        });

        Schema::table('estore_order_items', function (Blueprint $table) {
            // Drop nullable column
            $table->dropColumn('product_id');
        });

        Schema::table('estore_order_items', function (Blueprint $table) {
            // Restore product_id as NOT NULL with cascade delete
            $table->unsignedBigInteger('product_id')->after('order_id');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
}
