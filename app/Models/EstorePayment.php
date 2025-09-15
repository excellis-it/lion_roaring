<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstorePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'stripe_payment_intent_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_details',
        'paid_at',
        'payment_type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(EstoreOrder::class, 'order_id');
    }
}
