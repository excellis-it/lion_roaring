<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderEmailTemplateToMembershipMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->string('renewal_reminder_subject')->nullable()->after('renewal_reminder_days');
            $table->longText('renewal_reminder_body')->nullable()->after('renewal_reminder_subject');
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
            $table->dropColumn(['renewal_reminder_subject', 'renewal_reminder_body']);
        });
    }
}
