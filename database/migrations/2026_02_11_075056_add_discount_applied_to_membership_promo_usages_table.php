<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAppliedToMembershipPromoUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_promo_usages', function (Blueprint $table) {
            $table->decimal('discount_applied', 10, 2)->after('subscription_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_promo_usages', function (Blueprint $table) {
            $table->dropColumn('discount_applied');
        });
    }
}
