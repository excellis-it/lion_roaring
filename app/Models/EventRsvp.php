<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRsvp extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'rsvp_date',
        'notes',
    ];

    protected $casts = [
        'rsvp_date' => 'datetime',
    ];

    /**
     * Get the event that owns the RSVP.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns the RSVP.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with this RSVP.
     */
    public function payment()
    {
        return $this->hasOne(EventPayment::class, 'rsvp_id');
    }

    /**
     * Scope a query to only include confirmed RSVPs.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include pending RSVPs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include cancelled RSVPs.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
