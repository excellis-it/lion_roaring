<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('order_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Template title
            $table->string('slug')->unique(); // e.g., pending_order_email
            $table->unsignedBigInteger('order_status_id')->nullable(); // Optional relation to order status
            $table->text('subject'); // Email subject
            $table->longText('body'); // Email body / HTML
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key to order_statuses
            $table->foreign('order_status_id')
                ->references('id')
                ->on('order_statuses')
                ->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_email_templates');
    }
}
