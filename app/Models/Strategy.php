<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use HasFactory;


    // set created by name as created_by_full_name
    protected $appends = ['created_by_full_name'];

    // user who uploaded the file
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // created by user full name

    public function getCreatedByFullNameAttribute()
    {
        return $this->user ? $this->user->full_name : null;
    }
}
