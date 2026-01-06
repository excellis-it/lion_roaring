<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventFieldsToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('type', ['free', 'paid'])->default('free')->after('country_id');
            $table->decimal('price', 10, 2)->nullable()->after('type');
            $table->integer('capacity')->nullable()->after('price');
            $table->string('access_link')->unique()->nullable()->after('capacity');
            $table->boolean('send_notification')->default(true)->after('access_link');
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
            $table->dropColumn(['type', 'price', 'capacity', 'access_link', 'send_notification']);
        });
    }
}
