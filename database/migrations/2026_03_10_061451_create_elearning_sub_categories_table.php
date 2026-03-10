<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElearningSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elearning_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('elearning_category_id')->nullable(); // must be nullable
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('elearning_category_id')
                ->references('id')
                ->on('elearning_categories')
                ->nullOnDelete(); // or ->onDelete('set null')
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elearning_sub_categories');
    }
}
