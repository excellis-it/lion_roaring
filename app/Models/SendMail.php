<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendMail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'reply_of',
        'form_id',
        'to',
        'cc',
        'subject',
        'message',
        'attachment',
        'is_draft',
        'is_delete',
        'deleted_at',
        'created_at',
        'updated_at',

    ];

    protected function resolveUserTimezone(?string $tz): string
    {
        // some common legacy mappings
        $aliases = [
            'Asia/Calcutta' => 'Asia/Kolkata',
            // add more if you need…
        ];

        // map deprecated → correct
        $tz = $aliases[$tz] ?? $tz;

        // final check
        return in_array($tz, DateTimeZone::listIdentifiers())
            ? $tz
            : config('app.timezone');
    }

    public function getCreatedAtAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        return Carbon::parse($value)->timezone($tz);
    }

    public function getUpdatedAtAttribute($value)
    {
        $tz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        return Carbon::parse($value)->timezone($tz);
    }

    public function getDeletedAtAttribute($value)
    {
        $timezone = auth()->check()
        ? $this->resolveUserTimezone(auth()->user()->time_zone)
        : config('app.timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
    public function mailUsers()
    {
        return $this->hasMany(MailUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'form_id');
    }

    public function replies()
    {
        return $this->hasMany(SendMail::class, 'reply_of');
    }

    public function mainMail()
    {
        return $this->belongsTo(SendMail::class, 'reply_of');
    }

    public function userSender()
    {
        return $this->belongsTo(User::class, 'form_id');
    }

    public function lastReply()
    {
        return $this->hasMany(SendMail::class, 'reply_of', 'id')->latest();
    }
}
