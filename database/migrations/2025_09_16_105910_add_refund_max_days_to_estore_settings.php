<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundMaxDaysToEstoreSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            //
            $table->integer('refund_max_days')->after('credit_card_percentage')->default(10);
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
            $table->dropColumn('refund_max_days');
        });
    }
}
