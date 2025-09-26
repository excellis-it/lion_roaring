<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderLogoToEcomHomeCms extends Migration
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
            $table->string('header_logo')->nullable()->after('id');
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
            $table->dropColumn('header_logo');
        });
    }
}
