<?php

namespace App\Models;

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
        'is_to',
        'is_cc',
        'deleted_at',
    ];


    public function sendMail()
    {
        return $this->belongsTo(SendMail::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
