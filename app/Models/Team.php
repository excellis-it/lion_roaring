<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'group_image',
        'description',
        'created_at',
        'updated_at',
        'new_created_at'
    ];

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id');
    }

    public function chats()
    {
        return $this->hasMany(TeamChat::class, 'team_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }


    public function lastMessage()
    {
        return $this->hasOne(TeamChat::class, 'team_id', 'id')->latest();
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
}
