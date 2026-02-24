<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected  $commands = [
        'App\Console\Commands\DeleteJob',
        '\App\Console\Commands\UpdateDeletedMailsStatus',
        '\App\Console\Commands\MakeModelMigration',
        '\App\Console\Commands\SendSubscriptionReminder',
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('delete:job')->daily();
        $schedule->command('mails:update-deleted-status')->daily();
        $schedule->command('subscription:send-reminder --days=7')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
