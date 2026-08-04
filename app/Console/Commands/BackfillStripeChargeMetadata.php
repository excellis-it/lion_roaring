<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPayment;
use Illuminate\Console\Command;
use Stripe\Charge;
use Stripe\Stripe;

/**
 * Legacy web membership charges were created without customer/metadata, so they show up blank
 * in Stripe exports. Repairs them from subscription_payments (transaction_id -> user).
 * Stripe does not allow setting `customer` after the fact, so the email lands in metadata;
 * --with-receipt-email additionally fills the export's Customer Email column, at the cost of
 * Stripe emailing a receipt for a months-old charge.
 */
class BackfillStripeChargeMetadata extends Command
{
    protected $signature = 'stripe:backfill-charges
                            {--since= : Only payments created on/after this date (Y-m-d)}
                            {--until= : Only payments created on/before this date (Y-m-d)}
                            {--with-receipt-email : Also set receipt_email — Stripe emails the customer a receipt for these old charges}
                            {--dry-run : Report what would change without writing to Stripe}';

    protected $description = 'Backfill email + metadata on legacy Stripe charges';

    public function handle(): int
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $dryRun = (bool) $this->option('dry-run');
        $withReceiptEmail = (bool) $this->option('with-receipt-email');

        $query = SubscriptionPayment::with(['user', 'userSubscription.tier'])
            ->where('transaction_id', 'like', 'ch\_%')
            ->where('payment_status', 'Success');

        if ($since = $this->option('since')) {
            $query->whereDate('created_at', '>=', $since);
        }
        if ($until = $this->option('until')) {
            $query->whereDate('created_at', '<=', $until);
        }

        $payments = $query->orderBy('id')->get();
        $this->info($payments->count() . ' charge payment(s) to inspect' . ($dryRun ? ' (dry run)' : ''));

        $updated = $skipped = $failed = 0;

        foreach ($payments as $payment) {
            $user = $payment->user;
            if (!$user || !$user->email) {
                $this->warn($payment->transaction_id . ' — no user/email on record, skipped');
                $skipped++;
                continue;
            }

            try {
                $charge = Charge::retrieve($payment->transaction_id);
            } catch (\Throwable $e) {
                $this->error($payment->transaction_id . ' — retrieve failed: ' . $e->getMessage());
                $failed++;
                continue;
            }

            $existing = $charge->metadata ? $charge->metadata->toArray() : [];
            if (!empty($existing['user_id']) && (!$withReceiptEmail || !empty($charge->receipt_email))) {
                $skipped++;
                continue;
            }

            $tier = $payment->userSubscription?->tier;
            $params = [
                'metadata' => array_merge($existing, array_filter([
                    'user_id' => (string) $user->id,
                    'tier_id' => $tier ? (string) $tier->id : null,
                    'tier_name' => $tier?->name,
                    'billing_period' => $payment->billing_period,
                    'type' => 'membership',
                    'email' => $user->email,
                    'backfilled' => '1',
                ], static fn ($v) => $v !== null && $v !== '')),
            ];

            if ($withReceiptEmail) {
                $params['receipt_email'] = $user->email;
            }

            if ($dryRun) {
                $this->line($payment->transaction_id . ' -> ' . $user->email . ' ' . json_encode($params['metadata']));
                $updated++;
                continue;
            }

            try {
                Charge::update($payment->transaction_id, $params);
                $this->line($payment->transaction_id . ' updated (' . $user->email . ')');
                $updated++;
            } catch (\Throwable $e) {
                $this->error($payment->transaction_id . ' — update failed: ' . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Updated: {$updated}  Skipped: {$skipped}  Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
