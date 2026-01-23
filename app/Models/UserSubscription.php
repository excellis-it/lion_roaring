<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_method',
        'subscription_name',
        'subscription_price',
        'life_force_energy_tokens',
        'agree_accepted_at',
        'agree_description_snapshot',
        'subscription_validity',
        'subscription_start_date',
        'subscription_expire_date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'user_subscription_id');
    }
}
