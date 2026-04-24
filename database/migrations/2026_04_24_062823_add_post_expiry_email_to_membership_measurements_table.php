<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostExpiryEmailToMembershipMeasurementsTable extends Migration
{
    public function up()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->string('post_expiry_reminder_subject')->nullable()->after('renewal_reminder_body');
            $table->text('post_expiry_reminder_body')->nullable()->after('post_expiry_reminder_subject');
        });
    }

    public function down()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->dropColumn(['post_expiry_reminder_subject', 'post_expiry_reminder_body']);
        });
    }
}
