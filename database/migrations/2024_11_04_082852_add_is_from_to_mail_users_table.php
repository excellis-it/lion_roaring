<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFromToMailUsersTable extends Migration
{
    public function up()
    {
        Schema::table('mail_users', function (Blueprint $table) {
            $table->boolean('is_from')->default(false)->after('is_delete'); // Add is_from column
        });
    }

    public function down()
    {
        Schema::table('mail_users', function (Blueprint $table) {
            $table->dropColumn('is_from'); // Remove is_from column
        });
    }
}
