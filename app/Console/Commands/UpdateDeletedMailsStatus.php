<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateDeletedMailsStatus extends Command
{
    // Define the name and description of the command
    protected $signature = 'mails:update-deleted-status';
    protected $description = 'Set is_delete = 2 for records where deleted_at is more than 30 days ago and is_delete = 1';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Calculate the date 30 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Update the `is_delete` field to 2 for records that meet the criteria
        $affectedRows = DB::table('mail_users')
            ->where('is_delete', 1)
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $thirtyDaysAgo)
            ->update(['is_delete' => 2]);

        // Output the result for logging purposes
        $this->info("Updated {$affectedRows} records to is_delete = 2.");
    }
}
