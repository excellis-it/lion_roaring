<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->index(['sender_id', 'reciver_id', 'id'], 'chats_sender_reciver_id_idx');
            $table->index(['reciver_id', 'seen', 'sender_id'], 'chats_reciver_seen_sender_idx');
        });

        Schema::table('team_chats', function (Blueprint $table) {
            $table->index(['team_id', 'id'], 'team_chats_team_id_idx');
        });

        Schema::table('chat_members', function (Blueprint $table) {
            $table->index(['user_id', 'is_seen', 'chat_id'], 'chat_members_user_seen_chat_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex('chats_sender_reciver_id_idx');
            $table->dropIndex('chats_reciver_seen_sender_idx');
        });

        Schema::table('team_chats', function (Blueprint $table) {
            $table->dropIndex('team_chats_team_id_idx');
        });

        Schema::table('chat_members', function (Blueprint $table) {
            $table->dropIndex('chat_members_user_seen_chat_idx');
        });
    }
};
