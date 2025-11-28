<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTopicIdToElearningProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            $table->foreignId('elearning_topic_id')->nullable()->constrained('elearning_topics')->onDelete('set null')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elearning_products', function (Blueprint $table) {
            $table->dropForeign(['elearning_topic_id']);
            $table->dropColumn('elearning_topic_id');
        });
    }
}
