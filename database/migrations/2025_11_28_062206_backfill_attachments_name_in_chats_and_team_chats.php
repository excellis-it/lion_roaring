<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillAttachmentsNameInChatsAndTeamChats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Backfill 'attachment_name' from the existing 'attachment' path if it's null.
        if (Schema::hasColumn('chats', 'attachment')) {
            DB::table('chats')->whereNotNull('attachment')->whereNull('attachment_name')
                ->select('id', 'attachment')
                ->orderBy('id')
                ->chunkById(200, function ($items) {
                    foreach ($items as $item) {
                        $basename = pathinfo($item->attachment, PATHINFO_BASENAME);
                        DB::table('chats')->where('id', $item->id)
                            ->update(['attachment_name' => $basename]);
                    }
                });
        }

        if (Schema::hasColumn('team_chats', 'attachment')) {
            DB::table('team_chats')->whereNotNull('attachment')->whereNull('attachment_name')
                ->select('id', 'attachment')
                ->orderBy('id')
                ->chunkById(200, function ($items) {
                    foreach ($items as $item) {
                        $basename = pathinfo($item->attachment, PATHINFO_BASENAME);
                        DB::table('team_chats')->where('id', $item->id)
                            ->update(['attachment_name' => $basename]);
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally not reverting values set in this data migration.
    }
}
