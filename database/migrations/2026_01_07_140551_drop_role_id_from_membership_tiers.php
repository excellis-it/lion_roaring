<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropRoleIdFromMembershipTiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('membership_tiers', 'role_id')) {
            Schema::table('membership_tiers', function (Blueprint $table) {
                // safe drop — column was nullable and had no FK in original migration
                $table->dropColumn('role_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('membership_tiers', 'role_id')) {
            Schema::table('membership_tiers', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('cost');
            });
        }
    }
}
