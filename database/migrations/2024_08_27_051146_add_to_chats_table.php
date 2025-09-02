<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->boolean('deleted_for_sender')->default(0)->after('message'); // New column
            $table->boolean('deleted_for_reciver')->default(0)->after('deleted_for_sender'); // New column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('deleted_for_sender'); // Drop column
            $table->dropColumn('deleted_for_reciver'); // Drop column
        });
    }
}
