<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateCollaboration extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'create_zoom' => 'boolean',
        'is_zoom' => 'boolean',
    ];

    /**
     * Get the user who created this collaboration
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all invitations for this collaboration
     */
    public function invitations()
    {
        return $this->hasMany(CollaborationInvitation::class, 'collaboration_id');
    }

    /**
     * Get users who have been invited to this collaboration
     */
    public function invitedUsers()
    {
        return $this->belongsToMany(User::class, 'collaboration_invitations', 'collaboration_id', 'user_id')
            ->withPivot('status', 'accepted_at')
            ->withTimestamps();
    }

    /**
     * Get users who have accepted the invitation
     */
    public function acceptedUsers()
    {
        return $this->belongsToMany(User::class, 'collaboration_invitations', 'collaboration_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withPivot('status', 'accepted_at')
            ->withTimestamps();
    }

    /**
     * Check if a user has accepted the invitation
     */
    public function hasUserAccepted($userId)
    {
        return $this->invitations()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Check if user is the creator
     */
    public function isCreator($userId)
    {
        return $this->user_id == $userId;
    }
}
