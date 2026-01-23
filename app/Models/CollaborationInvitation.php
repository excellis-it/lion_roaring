<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollaborationInvitation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'collaboration_id',
        'user_id',
        'status',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the collaboration for this invitation
     */
    public function collaboration()
    {
        return $this->belongsTo(PrivateCollaboration::class, 'collaboration_id');
    }

    /**
     * Get the user who received this invitation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Mark invitation as accepted
     */
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Mark invitation as declined
     */
    public function decline()
    {
        $this->update([
            'status' => 'declined',
        ]);
    }
}
