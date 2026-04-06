<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSealAndStewardFieldsToRegisterAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_agreements', function (Blueprint $table) {
            $table->string('seal_image')->nullable()->after('checkbox_text');
            $table->string('steward_member_1')->nullable()->after('seal_image');
            $table->string('steward_member_2')->nullable()->after('steward_member_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_agreements', function (Blueprint $table) {
            $table->dropColumn(['seal_image', 'steward_member_1', 'steward_member_2']);
        });
    }
}
