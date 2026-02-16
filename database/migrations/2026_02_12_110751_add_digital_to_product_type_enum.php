<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDigitalToProductTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify the product_type enum to include 'digital'
        \DB::statement("ALTER TABLE `products` MODIFY COLUMN `product_type` ENUM('simple', 'variable', 'digital') NOT NULL DEFAULT 'simple'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original enum values
        \DB::statement("ALTER TABLE `products` MODIFY COLUMN `product_type` ENUM('simple', 'variable') NOT NULL DEFAULT 'simple'");
    }
}
