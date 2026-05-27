<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipCardTitleToMembershipMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_measurements', function (Blueprint $table) {
            $table->string('membership_card_title')->nullable()->default('My Current Membership')->after('yearly_dues');
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
            $table->dropColumn('membership_card_title');
        });
    }
}
