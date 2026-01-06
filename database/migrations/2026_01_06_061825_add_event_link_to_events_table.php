<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventLinkToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop old access_link column
            $table->dropUnique(['access_link']);
            $table->dropColumn('access_link');

            // Add new encrypted event_link column
            $table->text('event_link')->nullable()->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_link');
            $table->string('access_link')->unique()->nullable()->after('capacity');
        });
    }
}
