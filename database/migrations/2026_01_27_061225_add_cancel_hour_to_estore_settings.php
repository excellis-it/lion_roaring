<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelHourToEstoreSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('estore_settings', 'cancel_within_hours')) {
                $table->integer('cancel_within_hours')->nullable()->default(24)->after('refund_max_days')->comment('Order cancellation window in hours');
            }
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
            if (Schema::hasColumn('estore_settings', 'cancel_within_hours')) {
                $table->dropColumn('cancel_within_hours');
            }
        });
    }
}
