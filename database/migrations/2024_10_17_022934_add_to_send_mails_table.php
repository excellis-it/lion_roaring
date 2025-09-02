<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToSendMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->boolean('is_draft')->default(0)->after('attachment');
            $table->boolean('is_delete')->default(0)->after('is_draft');
            $table->timestamp('deleted_at')->nullable()->after('is_delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->dropColumn('is_draft');
            $table->dropColumn('is_delete');
            $table->dropColumn('deleted_at');
        });
    }
}
