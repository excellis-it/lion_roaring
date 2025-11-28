<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentNameToChatsAndTeams extends Migration
{
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment');
            }
        });

        Schema::table('team_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('team_chats', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment');
            }
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'attachment_name')) {
                $table->dropColumn('attachment_name');
            }
        });

        Schema::table('team_chats', function (Blueprint $table) {
            if (Schema::hasColumn('team_chats', 'attachment_name')) {
                $table->dropColumn('attachment_name');
            }
        });
    }
}
