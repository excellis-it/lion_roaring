<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchTypeToChatbotKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatbot_keywords', function (Blueprint $table) {
            $table->string('search_type')->default('others')->after('keyword'); // estore, elearning, others
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_keywords', function (Blueprint $table) {
            $table->dropColumn('search_type');
        });
    }
}
