<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHostMeetingLinkToPrivateCollaborationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->text('host_meeting_link')->nullable()->after('meeting_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->dropColumn('host_meeting_link');
        });
    }
}
