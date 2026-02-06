<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedDeliveryDaysToEstoreSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            $table->integer('expected_delivery_days')->default(7)->after('cancel_within_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            $table->dropColumn('expected_delivery_days');
        });
    }
}
