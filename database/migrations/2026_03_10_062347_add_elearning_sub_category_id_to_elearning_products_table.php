<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElearningSubCategoryIdToElearningProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            $table->unsignedBigInteger('elearning_sub_category_id')->nullable()->after('category_id');
            $table->foreign('elearning_sub_category_id')->references('id')->on('elearning_sub_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            $table->dropForeign(['elearning_sub_category_id']);
            $table->dropColumn('elearning_sub_category_id');
        });
    }
}
