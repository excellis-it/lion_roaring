<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColsToEcomHomeCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ecom_home_cms', function (Blueprint $table) {
            //
            $table->string('banner_image_small')->nullable()->after('banner_image');
            $table->string('product_category_image')->nullable()->before('product_category_title');
            $table->string('featured_product_image')->nullable()->before('featured_product_title');
            $table->string('new_product_image')->nullable()->before('new_product_title');
            $table->string('new_arrival_image')->nullable()->after('featured_product_subtitle');
            $table->string('new_arrival_title')->nullable()->after('new_arrival_image');
            $table->text('new_arrival_subtitle')->nullable()->after('new_arrival_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ecom_home_cms', function (Blueprint $table) {
            //
            $table->dropColumn('banner_image_small');
            $table->dropColumn('product_category_image');
            $table->dropColumn('featured_product_image');
            $table->dropColumn('new_product_image');
            $table->dropColumn('new_arrival_image');
            $table->dropColumn('new_arrival_title');
            $table->dropColumn('new_arrival_subtitle');
        });
    }
}
