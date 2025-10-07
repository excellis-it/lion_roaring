<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoCodesToEstoreOrders extends Migration
{
    public function up(): void
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            $table->string('promo_code')->nullable()->after('warehouse_address');
            $table->decimal('promo_discount', 8, 2)->default(0)->after('promo_code');
        });
    }

    public function down(): void
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            $table->dropColumn(['promo_code', 'promo_discount']);
        });
    }
}
