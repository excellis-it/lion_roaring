<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(TeamChat::class, 'chat_id' , 'id');
    }
}
