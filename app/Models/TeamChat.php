<?php

namespace App\Models;

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
    ];


    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
