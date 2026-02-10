<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteUpdateToSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->tinyInteger('SITE_UPDATE')
                ->nullable()
                ->default(0)
                ->comment('0 = Inactive, 1 = Active')
                ->after('DONATE_BANK_TRANSFER_DETAILS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('SITE_UPDATE');
        });
    }
}
