<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // add location_lat,location_lng,location_address,location_zip,location_country,location_state
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->string('location_address')->nullable();
            $table->string('location_zip')->nullable();
            $table->string('location_country')->nullable();
            $table->string('location_state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['location_lat', 'location_lng', 'location_address', 'location_zip', 'location_country', 'location_state']);
        });
    }
}
