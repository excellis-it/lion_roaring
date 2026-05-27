<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElearningOrdersAndItemsTables extends Migration
{
    public function up()
    {
        Schema::create('elearning_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('promo_discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('promo_code')->nullable();
            $table->string('payment_status')->default('pending'); // pending|paid|failed|refunded
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'payment_status']);
        });

        Schema::create('elearning_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elearning_order_id')->constrained('elearning_orders')->cascadeOnDelete();
            $table->foreignId('elearning_product_id')->constrained('elearning_products')->cascadeOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('elearning_order_items');
        Schema::dropIfExists('elearning_orders');
    }
}
