<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcomCmsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecom_cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->nullable();
            $table->string('page_title')->nullable();
            $table->longText('page_content')->nullable();
            $table->string('page_banner_image')->nullable();
            $table->string('slug')->nullable()->unique();
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
        Schema::dropIfExists('ecom_cms_pages');
    }
}
