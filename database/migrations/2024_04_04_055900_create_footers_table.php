<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFootersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->string('footer_logo')->nullable();
            $table->text('footer_title')->nullable();
            $table->text('footer_playstore_link')->nullable();
            $table->string('footer_playstore_icon')->nullable();
            $table->text('footer_appstore_link')->nullable();
            $table->string('footer_appstore_icon')->nullable();
            $table->text('footer_newsletter_title')->nullable();
            $table->text('footer_address_title')->nullable();
            $table->text('footer_address')->nullable();
            $table->string('footer_phone_number')->nullable();
            $table->string('footer_email')->nullable();
            $table->text('footer_copywrite_text')->nullable();
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
        Schema::dropIfExists('footers');
    }
}
