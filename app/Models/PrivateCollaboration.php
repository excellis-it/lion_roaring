<?php

namespace App\Models;

use App\Models\Concerns\SafeDateTimes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrivateCollaboration extends BaseModel
{
    use HasFactory;
    use SafeDateTimes;

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'meeting_link',
        'start_time',
        'end_time',
        'user_id',
        'create_zoom',
        'is_zoom',
    ];

    protected $casts = [
        'create_zoom' => 'boolean',
        'is_zoom' => 'boolean',
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
        return $this->parseStoredDateTime($value);
    }

    public function getEndTimeAttribute($value): ?Carbon
    {
        return $this->parseStoredDateTime($value);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(CollaborationInvitation::class, 'collaboration_id');
    }

    public function invitedUsers()
    {
        return $this->belongsToMany(User::class, 'collaboration_invitations', 'collaboration_id', 'user_id')
            ->withPivot('status', 'accepted_at')
            ->withTimestamps();
    }

    public function acceptedUsers()
    {
        return $this->belongsToMany(User::class, 'collaboration_invitations', 'collaboration_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withPivot('status', 'accepted_at')
            ->withTimestamps();
    }

    public function hasUserAccepted($userId)
    {
        return $this->invitations()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->exists();
    }

    public function isCreator($userId)
    {
        return $this->user_id == $userId;
    }
}
