<?php

namespace App\Console\Commands;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Job deleted successfully!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // delete coloumn from job table which created at 30 days ago
        $jobs = Job::where('created_at', '<', Carbon::now()->subDays(30))->delete();

        $this->info('Job deleted successfully!');
        Log::info('Job deleted successfully!');
    }
}
