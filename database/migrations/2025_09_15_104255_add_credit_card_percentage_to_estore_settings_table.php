<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditCardPercentageToEstoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
             $table->decimal('credit_card_percentage', 5, 2)->default(0.00)->after('is_pickup_available');
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
             $table->dropColumn('credit_card_percentage');
        });
    }
}
