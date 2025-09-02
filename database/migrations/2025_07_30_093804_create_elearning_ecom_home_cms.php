<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElearningEcomHomeCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elearning_ecom_home_cms', function (Blueprint $table) {
            $table->id();
            $table->string('banner_image')->nullable();
            $table->string('banner_title')->nullable();
            $table->text('banner_subtitle')->nullable();
            $table->string('product_category_title')->nullable();
            $table->text('product_category_subtitle')->nullable();
            $table->string('featured_product_title')->nullable();
            $table->text('featured_product_subtitle')->nullable();
            $table->string('new_product_title')->nullable();
            $table->text('new_product_subtitle')->nullable();
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
        Schema::dropIfExists('elearning_ecom_home_cms');
    }
}
