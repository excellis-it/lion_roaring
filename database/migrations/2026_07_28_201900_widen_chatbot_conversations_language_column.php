<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow UI sentinel values like __original__ (12 chars) and ISO codes with region.
     * Column was VARCHAR(10) and rejected language = '__original__' on chatbot init.
     */
    public function up(): void
    {
        if (! Schema::hasTable('chatbot_conversations')) {
            return;
        }

        DB::statement("ALTER TABLE `chatbot_conversations` MODIFY COLUMN `language` VARCHAR(20) NOT NULL DEFAULT 'en'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('chatbot_conversations')) {
            return;
        }

        DB::statement("ALTER TABLE `chatbot_conversations` MODIFY COLUMN `language` VARCHAR(10) NOT NULL DEFAULT 'en'");
    }
};
