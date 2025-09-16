<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserIdOnEstoreOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['user_id']);
        });

        Schema::table('estore_orders', function (Blueprint $table) {
            // Drop the column and re-add it as nullable
            $table->dropColumn('user_id');
        });

        Schema::table('estore_orders', function (Blueprint $table) {
            // Recreate user_id as nullable with foreign key SET NULL
            $table->unsignedBigInteger('user_id')->nullable()->after('warehouse_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('estore_orders', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['user_id']);
        });

        Schema::table('estore_orders', function (Blueprint $table) {
            // Drop nullable column
            $table->dropColumn('user_id');
        });

        Schema::table('estore_orders', function (Blueprint $table) {
            // Restore user_id as NOT NULL with cascade delete
            $table->unsignedBigInteger('user_id')->after('warehouse_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
}
