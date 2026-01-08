<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionsToMembershipTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->longText('permissions')->nullable()->after('agree_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
}
