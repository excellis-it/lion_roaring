<?php

namespace App\Models;

use App\Models\Concerns\SafeDateTimes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends BaseModel
{
    use HasFactory;
    use SafeDateTimes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start',
        'end',
        'time_zone',
        'country_id',
        'type',
        'price',
        'capacity',
        'event_link',
        'send_notification',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'send_notification' => 'boolean',
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * Get all RSVPs for this event.
     */
    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    /**
     * Get confirmed RSVPs for this event.
     */
    public function confirmedRsvps()
    {
        return $this->hasMany(EventRsvp::class)->where('status', 'confirmed');
    }

    /**
     * Get all payments for this event.
     */
    public function payments()
    {
        return $this->hasMany(EventPayment::class);
    }

    /**
     * Get completed payments for this event.
     */
    public function completedPayments()
    {
        return $this->hasMany(EventPayment::class)->where('status', 'completed');
    }

    /**
     * Check if event has capacity.
     */
    public function hasCapacity()
    {
        if (!$this->capacity) {
            return true;
        }
        return $this->confirmedRsvps()->count() < $this->capacity;
    }

    /**
     * Get available spots.
     */
    public function availableSpots()
    {
        if (!$this->capacity) {
            return null;
        }
        return $this->capacity - $this->confirmedRsvps()->count();
    }

    /**
     * Get decrypted event link
     */
    public function getDecryptedLink()
    {
        if (!$this->event_link) {
            return null;
        }

        try {
            return decrypt($this->event_link);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted event link
     */
    public function setEncryptedLink($link)
    {
        if (!$link) {
            $this->event_link = null;
            return;
        }

        $this->event_link = encrypt($link);
    }


    public function setStartAttribute($value): void
    {
        $this->attributes['start'] = $this->normalizeDateTimeInput($value);
    }

    public function setEndAttribute($value): void
    {
        $this->attributes['end'] = $this->normalizeDateTimeInput($value);
    }

    public function getCreatedAtAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getUpdatedAtAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getStartAttribute($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getEndAttribute($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    /**
     * Get formatted start time with timezone
     */
    public function getFormattedStartAttribute()
    {
        if (!$this->start) return null;

        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        return $this->start->format('M j, Y g:i A T');
    }

    /**
     * Get formatted end time with timezone
     */
    public function getFormattedEndAttribute()
    {
        if (!$this->end) return null;

        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        return $this->end->format('M j, Y g:i A T');
    }

    /**
     * Check if event is paid
     */
    public function isPaid()
    {
        return $this->type === 'paid';
    }

    /**
     * Check if event is free
     */
    public function isFree()
    {
        return $this->type === 'free';
    }
}
