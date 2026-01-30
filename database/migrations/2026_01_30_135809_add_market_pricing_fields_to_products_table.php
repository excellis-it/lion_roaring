<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketPricingFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_market_priced')->default(false)->after('is_free');
            $table->foreignId('market_material_id')->nullable()->after('is_market_priced')->constrained('market_materials')->nullOnDelete();
            $table->decimal('market_grams', 12, 4)->nullable()->after('market_material_id');
            $table->decimal('market_rate_per_gram', 18, 8)->nullable()->after('market_grams');
            $table->timestamp('market_rate_at')->nullable()->after('market_rate_per_gram');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['market_material_id']);
            $table->dropColumn([
                'is_market_priced',
                'market_material_id',
                'market_grams',
                'market_rate_per_gram',
                'market_rate_at',
            ]);
        });
    }
}
