<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCountryIdColoumnToPrivateCollaborationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('user_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        $usa = DB::table('countries')->where('name', 'United States')->first();

        if ($usa) {
            DB::table('private_collaborations')->update([
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
        Schema::table('private_collaborations', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
}
