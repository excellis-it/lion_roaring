<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUserEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing users email with a new format and store the old email in personal_email field';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Generate the new email format
            $uniqueNumber = rand(1000, 9999);  // Generate a unique number
            $lrEmail = strtolower(trim($user->first_name)) . strtolower(trim($user->middle_name)) . strtolower(trim($user->last_name)) . $uniqueNumber . '@lionroaring.us';

            $user->personal_email = $lrEmail;

            // Save the updated user
            $user->save();

            // Output a message to the console
            $this->info("User {$user->id} updated: {$user->user_name} -> {$user->personal_email}");
        }

        $this->info('All user emails have been updated.');
    }
}
