<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPayment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'rsvp_id',
        'transaction_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_details',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the event that owns the payment.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the RSVP associated with this payment.
     */
    public function rsvp()
    {
        return $this->belongsTo(EventRsvp::class, 'rsvp_id');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
