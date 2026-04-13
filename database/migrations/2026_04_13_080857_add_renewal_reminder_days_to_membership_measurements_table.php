<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenewalReminderDaysToMembershipMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->unsignedInteger('renewal_reminder_days')->default(7)->after('membership_card_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->dropColumn('renewal_reminder_days');
        });
    }
}
