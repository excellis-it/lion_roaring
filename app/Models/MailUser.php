<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeZone;
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
}
