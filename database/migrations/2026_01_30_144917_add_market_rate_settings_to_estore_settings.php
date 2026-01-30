<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketRateSettingsToEstoreSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('estore_settings', 'market_rate_primary')) {
                $table->string('market_rate_primary')->default('metalpriceapi')->after('cancel_within_hours');
            }
            if (!Schema::hasColumn('estore_settings', 'market_rate_refresh_value')) {
                $table->unsignedInteger('market_rate_refresh_value')->default(12)->after('market_rate_primary');
            }
            if (!Schema::hasColumn('estore_settings', 'market_rate_refresh_unit')) {
                $table->string('market_rate_refresh_unit', 10)->default('hour')->after('market_rate_refresh_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_settings', function (Blueprint $table) {
            $cols = [
                'market_rate_primary',
                'market_rate_refresh_value',
                'market_rate_refresh_unit',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('estore_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
