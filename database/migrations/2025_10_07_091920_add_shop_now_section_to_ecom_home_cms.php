<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopNowSectionToEcomHomeCms extends Migration
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
            $table->string('shop_now_title')->nullable();
            $table->text('shop_now_description')->nullable();
            $table->string('shop_now_button_text')->nullable();
            $table->string('shop_now_button_link')->nullable();
            $table->string('shop_now_image')->nullable();
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
            $table->dropColumn('shop_now_title');
            $table->dropColumn('shop_now_description');
            $table->dropColumn('shop_now_button_text');
            $table->dropColumn('shop_now_button_link');
            $table->dropColumn('shop_now_image');
        });
    }
}
