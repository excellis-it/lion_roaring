<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUrlColumnToTextInUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->text('url')->change();
        });
    }

    public function down()
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('url', 255)->change();
        });
    }
}
