<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

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
        'access_link',
        'send_notification',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
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
     * Check if event is paid.
     */
    public function isPaid()
    {
        return $this->type === 'paid';
    }

    /**
     * Check if event is free.
     */
    public function isFree()
    {
        return $this->type === 'free';
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


    protected function resolveUserTimezone(?string $tz): string
    {
        // some common legacy mappings
        $aliases = [
            'Asia/Calcutta' => 'Asia/Kolkata',
            // add more if you need…
        ];

        // map deprecated → correct
        $tz = $aliases[$tz] ?? $tz;

        // final check
        return in_array($tz, DateTimeZone::listIdentifiers())
            ? $tz
            : config('app.timezone');
    }

    public function getCreatedAtAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');
        $datetime = Carbon::parse($value, $this->resolveUserTimezone($this->time_zone));
        return Carbon::parse($datetime)->timezone($tz);
    }

    public function getUpdatedAtAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');
        $datetime = Carbon::parse($value, $this->resolveUserTimezone($this->time_zone));
        return Carbon::parse($datetime)->timezone($tz);
    }
}
