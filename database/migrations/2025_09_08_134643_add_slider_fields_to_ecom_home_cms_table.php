<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSliderFieldsToEcomHomeCmsTable extends Migration
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
            $table->json('slider_data')->nullable()->after('new_arrival_image');
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
            $table->dropColumn('slider_data');
        });
    }
}
