<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\MembershipMeasurement;
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
    protected $signature = 'subscription:send-reminder {--days= : Number of days before expiry (optional override)}';

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
        $measurement = MembershipMeasurement::query()->first();
        $days = $this->resolveReminderDays($measurement);
        $targetDate = Carbon::today()->addDays($days);

        // Send reminders only for subscriptions expiring exactly N days from today.
        $subscriptions = UserSubscription::with(['user', 'tier'])
            ->whereNotNull('subscription_expire_date')
            ->whereRaw('DATE(subscription_expire_date) = ?', [$targetDate->toDateString()])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions expiring exactly in ' . $days . ' day(s).');
            Log::info('Subscription Reminder: No subscriptions expiring exactly in ' . $days . ' day(s).');
            return 0;
        }

        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if (!$user || !$user->email) {
                $failed++;
                continue;
            }

            try {
                $expireDate = Carbon::parse($subscription->subscription_expire_date)->startOfDay();
            } catch (\Throwable $e) {
                $skipped++;
                Log::warning('Subscription Reminder skipped due to invalid expire date for subscription ID ' . $subscription->id);
                continue;
            }

            // Prevent duplicates for the same subscription cycle.
            if (!empty($subscription->reminder_for_expire_date)) {
                try {
                    $remindedForDate = Carbon::parse($subscription->reminder_for_expire_date)->toDateString();
                    if ($remindedForDate === $expireDate->toDateString()) {
                        $skipped++;
                        continue;
                    }
                } catch (\Throwable $e) {
                    // Ignore malformed historical values and continue with send attempt.
                }
            }

            $daysRemaining = Carbon::today()->diffInDays($expireDate, false);

            if ($daysRemaining < 0) {
                $skipped++;
                continue;
            }

            $startDate = 'N/A';
            if (!empty($subscription->subscription_start_date)) {
                try {
                    $startDate = Carbon::parse($subscription->subscription_start_date)->format('F d, Y');
                } catch (\Throwable $e) {
                    $startDate = 'N/A';
                }
            }

            $maildata = [
                'name' => $user->name ?? $user->first_name ?? 'Member',
                'subscription_name' => $subscription->subscription_name ?? 'Membership',
                'start_date' => $startDate,
                'expire_date' => $expireDate->format('F d, Y'),
                'days_remaining' => (int) $daysRemaining,
                'renew_url' => route('user.membership.index'),
                'custom_subject' => $measurement->renewal_reminder_subject ?? null,
                'custom_body' => $measurement->renewal_reminder_body ?? null,
            ];

            try {
                Mail::to($user->email)->send(new SubscriptionReminderMail($maildata));
                $subscription->reminder_for_expire_date = $expireDate->toDateString();
                $subscription->reminder_sent_at = now();
                $subscription->save();

                $sent++;
                Log::info('Subscription Reminder sent to: ' . $user->email . ' (expires: ' . $maildata['expire_date'] . ')');
            } catch (\Throwable $e) {
                $failed++;
                Log::error('Subscription Reminder failed for: ' . $user->email . ' - ' . $e->getMessage());
                $this->error('Failed to send to: ' . $user->email . ' - ' . $e->getMessage());
            }
        }

        $this->info("Subscription reminder emails sent: {$sent}, skipped: {$skipped}, failed: {$failed}");
        Log::info("Subscription Reminder completed. Sent: {$sent}, Skipped: {$skipped}, Failed: {$failed}");

        return 0;
    }

    private function resolveReminderDays(?MembershipMeasurement $measurement = null): int
    {
        $daysOption = $this->option('days');

        if ($daysOption !== null && $daysOption !== '') {
            return max(1, (int) $daysOption);
        }

        $configuredDays = $measurement ? $measurement->renewal_reminder_days : MembershipMeasurement::query()->value('renewal_reminder_days');

        return max(1, (int) ($configuredDays ?? 7));
    }
}
