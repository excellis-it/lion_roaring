<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElearningOrder extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'promo_discount',
        'total_amount',
        'promo_code',
        'payment_status',
        'stripe_payment_intent_id',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'promo_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ELRN-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ElearningOrderItem::class);
    }
}
