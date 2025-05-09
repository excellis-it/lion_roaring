<?php

namespace App\Models;

use Carbon\Carbon;
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
        'created_at',
        'updated_at',
        'new_created_at'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reciver()
    {
        return $this->belongsTo(User::class, 'reciver_id');
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
