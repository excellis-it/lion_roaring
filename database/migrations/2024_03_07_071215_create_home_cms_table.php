<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_cms', function (Blueprint $table) {
            $table->id();
            $table->string('banner_title')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_video')->nullable();
            $table->string('section_1_title')->nullable();
            $table->string('section_1_sub_title')->nullable();
            $table->string('section_1_video')->nullable();
            $table->longText('section_1_description')->nullable();
            $table->string('section_2_left_title')->nullable();
            $table->string('section_2_left_image')->nullable();
            $table->longText('section_2_left_description')->nullable();
            $table->string('section_2_right_title')->nullable();
            $table->string('section_2_right_image')->nullable();
            $table->longText('section_2_right_description')->nullable();
            $table->string('section_3_title')->nullable();
            $table->longText('section_3_description')->nullable();
            $table->string('section_4_title')->nullable();
            $table->longText('section_4_description')->nullable();
            $table->string('section_5_title')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
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
        Schema::dropIfExists('home_cms');
    }
}
