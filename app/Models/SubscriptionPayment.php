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
        'payment_method',
        'payment_amount',
        'promo_code',
        'discount_amount',
        'payment_status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userSubscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }
}
