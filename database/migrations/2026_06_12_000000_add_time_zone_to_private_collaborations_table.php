<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTimeZoneToPrivateCollaborationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->string('time_zone')->default('UTC')->after('user_id');
        });

        DB::statement('
            UPDATE private_collaborations pc
            INNER JOIN users u ON u.id = pc.user_id
            SET pc.time_zone = COALESCE(NULLIF(u.time_zone, ""), "UTC")
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->dropColumn('time_zone');
        });
    }
}
