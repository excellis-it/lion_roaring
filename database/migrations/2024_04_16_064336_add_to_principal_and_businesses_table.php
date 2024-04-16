<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToPrincipalAndBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('principal_and_businesses', function (Blueprint $table) {
            $table->longText('description1')->nullable()->after('description');
            $table->longText('description2')->nullable()->after('description1');
            $table->longText('description3')->nullable()->after('description2');
            $table->longText('description4')->nullable()->after('description3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('principal_and_businesses', function (Blueprint $table) {
            $table->dropColumn('description1');
            $table->dropColumn('description2');
            $table->dropColumn('description3');
            $table->dropColumn('description4');
        });
    }
}
