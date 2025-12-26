<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCountryIdColoumnToStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('user_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });

        $usa = DB::table('countries')->where('name', 'United States')->first();

        if ($usa) {
            DB::table('strategies')->update([
                'country_id' => $usa->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
}
