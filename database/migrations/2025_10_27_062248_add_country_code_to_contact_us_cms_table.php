<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryCodeToContactUsCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_us_cms', function (Blueprint $table) {
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
        Schema::table('contact_us_cms', function (Blueprint $table) {
            //
            $table->dropColumn('country_code');
        });
    }
}
