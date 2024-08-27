<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'reciver_id',
        'message',
        'attachment',
        'seen',
        'deleted_for_sender',
        'deleted_for_reciver',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reciver()
    {
        return $this->belongsTo(User::class, 'reciver_id');
    }
}
