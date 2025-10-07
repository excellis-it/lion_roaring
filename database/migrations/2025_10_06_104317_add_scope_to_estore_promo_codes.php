<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScopeToEstorePromoCodes extends Migration
{
    public function up(): void
    {
        Schema::table('estore_promo_codes', function (Blueprint $table) {
            $table->string('scope_type')->default('all')->after('status');
            $table->json('user_ids')->nullable()->after('scope_type');
            $table->json('product_ids')->nullable()->after('user_ids');
        });
    }

    public function down(): void
    {
        Schema::table('estore_promo_codes', function (Blueprint $table) {
            $table->dropColumn(['scope_type', 'user_ids', 'product_ids']);
        });
    }
}
