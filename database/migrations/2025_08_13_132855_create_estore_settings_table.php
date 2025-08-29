<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEstoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estore_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('delivery_cost', 10, 2);
            $table->decimal('tax_percentage', 5, 2);
            $table->boolean('is_pickup_available')->default(false);
            $table->timestamps();
        });

        // insert default settings
        DB::table('estore_settings')->insert([
            'shipping_cost' => 0,
            'delivery_cost' => 0,
            'tax_percentage' => 0,
            'is_pickup_available' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estore_settings');
    }
}
