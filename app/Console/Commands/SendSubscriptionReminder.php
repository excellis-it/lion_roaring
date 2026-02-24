<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-reminder {--days=7 : Number of days before expiry to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users whose subscription is about to expire';

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
        $days = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($days);

        // Find active subscriptions that expire within the specified number of days
        $subscriptions = UserSubscription::with(['user', 'tier'])
            ->where('subscription_expire_date', '>', Carbon::now())
            ->where('subscription_expire_date', '<=', $targetDate)
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions expiring within the next ' . $days . ' day(s).');
            Log::info('Subscription Reminder: No subscriptions expiring within ' . $days . ' day(s).');
            return 0;
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if (!$user || !$user->email) {
                $failed++;
                continue;
            }

            $daysRemaining = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($subscription->subscription_expire_date)->startOfDay(), false);

            if ($daysRemaining < 0) {
                continue;
            }

            $maildata = [
                'name' => $user->name ?? $user->first_name ?? 'Member',
                'subscription_name' => $subscription->subscription_name ?? 'Membership',
                'start_date' => Carbon::parse($subscription->subscription_start_date)->format('F d, Y'),
                'expire_date' => Carbon::parse($subscription->subscription_expire_date)->format('F d, Y'),
                'days_remaining' => (int) $daysRemaining,
            ];

            try {
                Mail::to($user->email)->send(new SubscriptionReminderMail($maildata));
                $sent++;
                Log::info('Subscription Reminder sent to: ' . $user->email . ' (expires: ' . $maildata['expire_date'] . ')');
            } catch (\Throwable $e) {
                $failed++;
                Log::error('Subscription Reminder failed for: ' . $user->email . ' - ' . $e->getMessage());
                $this->error('Failed to send to: ' . $user->email . ' - ' . $e->getMessage());
            }
        }

        $this->info("Subscription reminder emails sent: {$sent}, failed: {$failed}");
        Log::info("Subscription Reminder completed. Sent: {$sent}, Failed: {$failed}");

        return 0;
    }
}
