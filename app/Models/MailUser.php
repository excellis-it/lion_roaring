<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'send_mail_id',
        'user_id',
        'is_read',
        'is_starred',
        'is_delete',
        'is_from',
        'is_to',
        'is_cc',
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    public function sendMail()
    {
        return $this->belongsTo(SendMail::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getDeletedAtAttribute($value)
    {
        $timezone = auth()->check() ? auth()->user()->time_zone : config('app.timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
}
