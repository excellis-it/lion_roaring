<?php

namespace App\Models;

use App\Models\Concerns\SafeDateTimes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends BaseModel
{
    use HasFactory;
    use SafeDateTimes;

    protected $fillable = [
        'user_id',
        'country_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'meeting_link',
        'time_zone',
        'created_at',
        'updated_at',
    ];

    public function setStartTimeAttribute($value): void
    {
        $this->attributes['start_time'] = $this->normalizeDateTimeInput($value);
    }

    public function setEndTimeAttribute($value): void
    {
        $this->attributes['end_time'] = $this->normalizeDateTimeInput($value);
    }

    public function getStartTimeAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getEndTimeAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getCreatedAtAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function getUpdatedAtAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value, $this->attributes['time_zone'] ?? $this->time_zone ?? null);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
