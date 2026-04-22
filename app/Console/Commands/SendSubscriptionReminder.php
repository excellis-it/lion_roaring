<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\MembershipMeasurement;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminder extends Command
{
    protected $signature = 'subscription:send-reminder {--days= : Number of days before expiry (optional override)}';
    protected $description = 'Send reminder emails to users whose subscription is about to expire or has expired; deactivate after 3 post-expiry reminders';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $measurement = MembershipMeasurement::query()->first();
        $adminEmail  = config('mail.from.address');

        $this->sendPreExpiryReminders($measurement, $adminEmail);
        $this->sendPostExpiryReminders($measurement, $adminEmail);

        return 0;
    }

    /**
     * Remind users whose membership expires exactly N days from today (pre-expiry).
     */
    private function sendPreExpiryReminders(?MembershipMeasurement $measurement, ?string $adminEmail): void
    {
        $days       = $this->resolveReminderDays($measurement);
        $targetDate = Carbon::today()->addDays($days);

        $subscriptions = UserSubscription::with(['user'])
            ->whereNotNull('subscription_expire_date')
            ->whereRaw('DATE(subscription_expire_date) = ?', [$targetDate->toDateString()])
            ->get();

        $sent = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user || !$user->email || !$user->status) {
                continue;
            }

            // Skip if already reminded for this expiry cycle
            if (!empty($subscription->reminder_for_expire_date)) {
                try {
                    $alreadyReminded = Carbon::parse($subscription->reminder_for_expire_date)->toDateString()
                        === Carbon::parse($subscription->subscription_expire_date)->toDateString();
                    if ($alreadyReminded && ($subscription->reminder_count ?? 0) >= 1) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            if ($this->dispatchReminder($subscription, $user, $measurement, $adminEmail, $days)) {
                $subscription->reminder_for_expire_date = Carbon::parse($subscription->subscription_expire_date)->toDateString();
                $subscription->reminder_sent_at = now();
                $subscription->reminder_count   = ($subscription->reminder_count ?? 0) + 1;
                $subscription->save();
                $sent++;
            }
        }

        Log::info("Subscription Pre-Expiry Reminder: sent={$sent} for {$days}-day window.");
    }

    /**
     * Remind users whose membership has already expired and haven't renewed.
     * After 3 total reminders, deactivate the account and notify admin.
     * Intervals: reminder #2 at +3 days after expiry, reminder #3 at +7 days after expiry.
     */
    private function sendPostExpiryReminders(?MembershipMeasurement $measurement, ?string $adminEmail): void
    {
        $today = Carbon::today();

        // Find expired subscriptions where user is still active and count < 3
        $subscriptions = UserSubscription::with(['user'])
            ->whereNotNull('subscription_expire_date')
            ->whereRaw('DATE(subscription_expire_date) < ?', [$today->toDateString()])
            ->where('reminder_count', '<', 3)
            ->get();

        $sent = 0;
        $deactivated = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user || !$user->email) {
                continue;
            }

            // Skip users who have already renewed (a newer active subscription exists)
            $hasActiveSubscription = UserSubscription::where('user_id', $user->id)
                ->whereRaw('DATE(subscription_expire_date) >= ?', [$today->toDateString()])
                ->exists();
            if ($hasActiveSubscription) {
                continue;
            }

            $expireDate     = Carbon::parse($subscription->subscription_expire_date)->startOfDay();
            $daysSinceExpiry = $expireDate->diffInDays($today, false);
            $count           = (int) ($subscription->reminder_count ?? 0);
            $lastSent        = $subscription->reminder_sent_at
                ? Carbon::parse($subscription->reminder_sent_at)
                : null;

            // Interval gates: remind at +3 days and +7 days after expiry
            // count=0 means no post-expiry reminder yet → send at +3 days
            // count=1 means 1 post-expiry reminder sent → send at +7 days
            // count=2 means 2 post-expiry reminders sent → send at +14 days, then deactivate
            $intervalMap = [0 => 3, 1 => 7, 2 => 14];
            $requiredDays = $intervalMap[$count] ?? 999;

            if ($daysSinceExpiry < $requiredDays) {
                continue;
            }

            // Prevent double-send on same day
            if ($lastSent && $lastSent->isToday()) {
                continue;
            }

            $newCount = $count + 1;

            if ($newCount >= 3) {
                // Third and final notice — send, then deactivate
                $this->dispatchReminder($subscription, $user, $measurement, $adminEmail, -$daysSinceExpiry, true);
                $subscription->reminder_sent_at = now();
                $subscription->reminder_count   = 3;
                $subscription->save();

                // Deactivate account
                $user->status = 0;
                $user->save();

                // Notify admin
                if ($adminEmail) {
                    try {
                        $adminData = [
                            'name'              => 'Admin',
                            'subscription_name' => $subscription->subscription_name ?? 'Membership',
                            'start_date'        => 'N/A',
                            'expire_date'       => $expireDate->format('F d, Y'),
                            'days_remaining'    => 0,
                            'renew_url'         => route('user.membership.index'),
                            'custom_subject'    => 'Account Deactivated – ' . ($user->full_name ?? $user->email) . ' (3 reminders sent)',
                            'custom_body'       => '<p>The account for <strong>' . e($user->full_name ?? $user->email) . '</strong> (' . e($user->email) . ') has been <strong>deactivated</strong> after 3 unpaid membership reminders.</p><p>Membership expired: ' . $expireDate->format('F d, Y') . '</p>',
                        ];
                        Mail::to($adminEmail)->send(new SubscriptionReminderMail($adminData));
                    } catch (\Throwable $e) {
                        Log::error('Admin deactivation notice failed for ' . $user->email . ': ' . $e->getMessage());
                    }
                }

                Log::info('Account deactivated after 3 reminders: ' . $user->email);
                $deactivated++;
            } else {
                if ($this->dispatchReminder($subscription, $user, $measurement, $adminEmail, -$daysSinceExpiry)) {
                    $subscription->reminder_sent_at = now();
                    $subscription->reminder_count   = $newCount;
                    $subscription->save();
                    $sent++;
                }
            }
        }

        Log::info("Subscription Post-Expiry Reminder: sent={$sent}, deactivated={$deactivated}.");
    }

    private function dispatchReminder(
        UserSubscription $subscription,
        User $user,
        ?MembershipMeasurement $measurement,
        ?string $adminEmail,
        int $daysRemaining,
        bool $isFinal = false
    ): bool {
        try {
            $expireDate = Carbon::parse($subscription->subscription_expire_date)->startOfDay();
            $startDate  = 'N/A';
            if (!empty($subscription->subscription_start_date)) {
                try {
                    $startDate = Carbon::parse($subscription->subscription_start_date)->format('F d, Y');
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $subject = $isFinal
                ? 'Final Notice: Your membership has expired — account will be deactivated'
                : ($measurement->renewal_reminder_subject ?? null);

            $maildata = [
                'name'              => $user->full_name ?? $user->first_name ?? 'Member',
                'subscription_name' => $subscription->subscription_name ?? 'Membership',
                'start_date'        => $startDate,
                'expire_date'       => $expireDate->format('F d, Y'),
                'days_remaining'    => max(0, (int) $daysRemaining),
                'renew_url'         => route('user.membership.index'),
                'custom_subject'    => $subject,
                'custom_body'       => $measurement->renewal_reminder_body ?? null,
            ];

            $mailer = Mail::to($user->email);
            if ($adminEmail && $adminEmail !== $user->email) {
                $mailer->cc($adminEmail);
            }
            $mailer->send(new SubscriptionReminderMail($maildata));

            Log::info('Subscription reminder sent to: ' . $user->email . ' (expires: ' . $expireDate->toDateString() . ', isFinal: ' . ($isFinal ? 'yes' : 'no') . ')');
            return true;
        } catch (\Throwable $e) {
            Log::error('Subscription reminder failed for: ' . $user->email . ' — ' . $e->getMessage());
            return false;
        }
    }

    private function resolveReminderDays(?MembershipMeasurement $measurement = null): int
    {
        $daysOption = $this->option('days');

        if ($daysOption !== null && $daysOption !== '') {
            return max(1, (int) $daysOption);
        }

        $configuredDays = $measurement
            ? $measurement->renewal_reminder_days
            : MembershipMeasurement::query()->value('renewal_reminder_days');

        return max(1, (int) ($configuredDays ?? 7));
    }
}
