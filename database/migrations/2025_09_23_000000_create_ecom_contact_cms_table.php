<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ecom_contact_cms', function (Blueprint $table) {
            $table->id();
            $table->string('banner_image')->nullable();
            $table->string('banner_title')->nullable();
            $table->string('card_one_title')->nullable();
            $table->text('card_one_content')->nullable();
            $table->string('card_two_title')->nullable();
            $table->text('card_two_content')->nullable();
            $table->string('card_three_title')->nullable();
            $table->text('card_three_content')->nullable();
            $table->string('form_title')->nullable();
            $table->text('form_subtitle')->nullable();
            $table->string('call_section_title')->nullable();
            $table->text('call_section_content')->nullable();
            $table->string('follow_us_title')->nullable();
            $table->text('map_iframe_src')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecom_contact_cms');
    }
};
