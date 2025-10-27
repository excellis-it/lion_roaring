<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryCodeToElearningEcomHomeCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elearning_ecom_home_cms', function (Blueprint $table) {
            //
            $table->string('country_code')->default('US')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elearning_ecom_home_cms', function (Blueprint $table) {
            //
            $table->dropColumn('country_code');
        });
    }
}
