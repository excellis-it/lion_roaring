<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainAndIsGlobalToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('domain')->nullable()->after('flag_image')
                ->comment('Full domain URL for this country (e.g., https://lionroaring.us)');
            $table->boolean('is_global')->default(false)->after('domain')
                ->comment('If true, this is the GLOBAL/main entry. Cannot be edited or deleted.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['domain', 'is_global']);
        });
    }
}
