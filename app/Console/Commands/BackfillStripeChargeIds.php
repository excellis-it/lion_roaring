<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPayment;
use Illuminate\Console\Command;
use Stripe\PaymentIntent;
use Stripe\Stripe;

/**
 * App payments are keyed on the PaymentIntent id (pi_), but Stripe's dashboard and CSV exports
 * show the charge id (ch_). Fills stripe_charge_id on existing rows so an admin can paste
 * either id into the payments search. Web charges already store ch_ in transaction_id.
 */
class BackfillStripeChargeIds extends Command
{
    protected $signature = 'stripe:backfill-charge-ids
                            {--dry-run : Report what would change without writing}';

    protected $description = 'Fill stripe_charge_id on payments recorded against a PaymentIntent';

    public function handle(): int
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $dryRun = (bool) $this->option('dry-run');

        $payments = SubscriptionPayment::query()
            ->where('transaction_id', 'like', 'pi\_%')
            ->whereNull('stripe_charge_id')
            ->orderBy('id')
            ->get();

        $this->info($payments->count() . ' payment(s) to inspect' . ($dryRun ? ' (dry run)' : ''));

        $updated = $skipped = $failed = 0;

        foreach ($payments as $payment) {
            try {
                $intent = PaymentIntent::retrieve($payment->transaction_id);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Legacy rows from a previous Stripe account — unresolvable, not an error.
                $this->warn($payment->transaction_id . ' — not on this Stripe account, skipped');
                $skipped++;
                continue;
            } catch (\Throwable $e) {
                $this->error($payment->transaction_id . ' — retrieve failed: ' . $e->getMessage());
                $failed++;
                continue;
            }

            $chargeId = $intent->latest_charge ? (string) $intent->latest_charge : null;
            if (!$chargeId) {
                $this->warn($payment->transaction_id . ' — no charge on intent (status ' . $intent->status . '), skipped');
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line($payment->transaction_id . ' -> ' . $chargeId);
                $updated++;
                continue;
            }

            $payment->stripe_charge_id = $chargeId;
            $payment->save();
            $this->line($payment->transaction_id . ' -> ' . $chargeId);
            $updated++;
        }

        $this->info("Updated: {$updated}  Skipped: {$skipped}  Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
