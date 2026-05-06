<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostExpiryIntervalsToMembershipMeasurementsTable extends Migration
{
    public function up()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->unsignedSmallInteger('post_expiry_interval_1_days')->default(3)->after('post_expiry_reminder_body');
            $table->unsignedSmallInteger('post_expiry_interval_2_days')->default(7)->after('post_expiry_interval_1_days');
            $table->unsignedSmallInteger('post_expiry_interval_3_days')->default(14)->after('post_expiry_interval_2_days');
        });
    }

    public function down()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->dropColumn([
                'post_expiry_interval_1_days',
                'post_expiry_interval_2_days',
                'post_expiry_interval_3_days',
            ]);
        });
    }
}
