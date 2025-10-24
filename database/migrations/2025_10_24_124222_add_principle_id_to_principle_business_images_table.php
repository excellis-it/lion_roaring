<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrincipleIdToPrincipleBusinessImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('principle_business_images', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('principle_id')->default(1)->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('principle_business_images', function (Blueprint $table) {
            //
            $table->dropColumn('principle_id');
        });
    }
}
