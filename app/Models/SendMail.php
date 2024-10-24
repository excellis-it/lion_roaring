<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendMail extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'to',
        'cc',
        'subject',
        'message',
        'attachment',
        'is_draft',
        'is_delete',
        'deleted_at',
    ];


    public function mailUsers()
    {
        return $this->hasMany(MailUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'form_id');
    }

}
