<?php

namespace App\Models;

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
}
