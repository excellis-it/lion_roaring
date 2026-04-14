<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileLocationToElearningProductsTable extends Migration
{
    public function up()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            if (!Schema::hasColumn('elearning_products', 'file_location')) {
                $table->string('file_location')->nullable()->after('specification');
            }
        });
    }

    public function down()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            if (Schema::hasColumn('elearning_products', 'file_location')) {
                $table->dropColumn('file_location');
            }
        });
    }
}
