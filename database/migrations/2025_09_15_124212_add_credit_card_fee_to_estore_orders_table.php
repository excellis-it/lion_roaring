<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditCardFeeToEstoreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            $table->decimal('credit_card_fee', 10, 2)
                  ->after('payment_status')
                  ->default(0)
                  ->comment('Credit card fee for the order');
                  $table->string('payment_type')->after('credit_card_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            $table->dropColumn('credit_card_fee');
            $table->dropColumn('payment_type');
        });
    }
}
