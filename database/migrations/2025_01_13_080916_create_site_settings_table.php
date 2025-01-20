<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('SITE_NAME')->default('Lion Roaring');
            $table->string('SITE_LOGO')->default('user_assets/images/logo.png');
            $table->string('SITE_CONTACT_EMAIL')->default('admin@lionroaring.us');
            $table->string('SITE_CONTACT_PHONE')->default('1 (240)-982-0054');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_settings');
    }
}
