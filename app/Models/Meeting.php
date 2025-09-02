<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'meeting_link',
        'time_zone',
        'created_at',
        'updated_at',
    ];

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

    public function getStartTimeAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');
        $datetime = Carbon::parse($value, $this->resolveUserTimezone($this->time_zone));
        return Carbon::parse($datetime)->timezone($tz);
    }


    public function getEndTimeAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');
        $datetime = Carbon::parse($value, $this->resolveUserTimezone($this->time_zone));
        return Carbon::parse($datetime)->timezone($tz);
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
