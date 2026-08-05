<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_subscription_id',
        'transaction_id',
        'stripe_charge_id',
        'payment_method',
        'payment_amount',
        'billing_period',
        'promo_code',
        'discount_amount',
        'payment_status',
    ];


    /**
     * A Stripe payment carries two ids: the PaymentIntent (pi_) we key payments on, and the
     * charge (ch_) that Stripe's dashboard and CSV exports show. Storing both lets an admin
     * paste either one into the payments search. Returns nothing when the column is missing,
     * so the code survives being deployed ahead of the migration.
     *
     * @return array{stripe_charge_id?: string}
     */
    public static function chargeIdAttrs(?string $chargeId): array
    {
        return $chargeId && \Illuminate\Support\Facades\Schema::hasColumn('subscription_payments', 'stripe_charge_id')
            ? ['stripe_charge_id' => $chargeId]
            : [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userSubscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }
}
