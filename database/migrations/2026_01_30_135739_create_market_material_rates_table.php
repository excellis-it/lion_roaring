<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketMaterialRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_material_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_material_id')->constrained('market_materials')->onDelete('cascade');
            $table->string('base_currency', 10)->default('USD');
            $table->decimal('usd_per_ounce', 18, 8)->nullable();
            $table->decimal('rate_per_gram', 18, 8)->nullable();
            $table->unsignedBigInteger('api_timestamp')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index(['market_material_id', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_material_rates');
    }
}
