<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page'); // e.g., 'footer', 'home_banner', 'about_us'
            $table->string('model_name'); // e.g., 'EcomFooterCms'
            $table->string('slug')->nullable(); // for multi-page CMS
            $table->string('country_code')->default('US'); // country code
            $table->json('content'); // store all CMS fields as JSON
            $table->timestamps();

            $table->unique(['page', 'country_code']); // ensures one entry per page/country
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_contents');
    }
}
