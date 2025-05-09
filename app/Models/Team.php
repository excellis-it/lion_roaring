<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getCreatedAtAttribute($value)
    {
        $timezone = auth()->check() ? auth()->user()->time_zone : config('app.timezone');
        return Carbon::parse($value)->timezone($timezone);
    }

    public function getUpdatedAtAttribute($value)
    {
        $timezone = auth()->check() ? auth()->user()->time_zone : config('app.timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
}
