<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewSectionColsToEcomHomeCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ecom_home_cms', function (Blueprint $table) {
            //
            $table->string('slider_data_second_title')->nullable();
            $table->json('slider_data_second')->nullable();
            $table->string('about_section_title')->nullable();
            $table->string('about_section_image')->nullable();
            $table->string('about_section_text_one_title')->nullable();
            $table->text('about_section_text_one_content')->nullable();
            $table->string('about_section_text_two_title')->nullable();
            $table->text('about_section_text_two_content')->nullable();
            $table->string('about_section_text_three_title')->nullable();
            $table->text('about_section_text_three_content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ecom_home_cms', function (Blueprint $table) {
            //
            $table->dropColumn([
                'slider_data_second_title',
                'slider_data_second',
                'about_section_title',
                'about_section_image',
                'about_section_text_one_title',
                'about_section_text_one_content',
                'about_section_text_two_title',
                'about_section_text_two_content',
                'about_section_text_three_title',
                'about_section_text_three_content'
            ]);
        });
    }
}
