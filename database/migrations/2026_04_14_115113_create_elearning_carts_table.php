<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElearningCartsTable extends Migration
{
    public function up()
    {
        Schema::create('elearning_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('elearning_product_id')->constrained('elearning_products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'elearning_product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('elearning_carts');
    }
}
