<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElearningEcomFooterCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elearning_ecom_footer_cms', function (Blueprint $table) {
            $table->id();
            $table->string('footer_logo')->nullable();
            $table->text('footer_title')->nullable();
            $table->text('footer_newsletter_title')->nullable();
            $table->text('footer_address_title')->nullable();
            $table->text('footer_address')->nullable();
            $table->string('footer_phone_number')->nullable();
            $table->string('footer_email')->nullable();
            $table->text('footer_copywrite_text')->nullable();
            $table->text('footer_facebook_link')->nullable();
            $table->text('footer_twitter_link')->nullable();
            $table->text('footer_instagram_link')->nullable();
            $table->text('footer_youtube_link')->nullable();
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
        Schema::dropIfExists('elearning_ecom_footer_cms');
    }
}
