<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'message',
        'attachment',
        'is_seen',
        'deleted_at',
        'created_at',
        'updated_at',
        'new_created_at'
    ];


    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function chatMembers()
    {
        return $this->hasMany(ChatMember::class, 'chat_id', 'id');
    }

    public function getNewCreatedAtAttribute($value)
    {
        return $this->created_at;
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

        return Carbon::parse($value)->timezone($tz);
    }

    public function getUpdatedAtAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        return Carbon::parse($value)->timezone($tz);
    }

    public function getDeletedAtAttribute($value)
    {
        $timezone = auth()->check()
        ? $this->resolveUserTimezone(auth()->user()->time_zone)
        : config('app.timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
}
