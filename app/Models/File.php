<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends BaseModel
{
    use HasFactory;

    // set created by name as created_by_full_name
    protected $appends = ['created_by_full_name'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // user who uploaded the file
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getCreatedByFullNameAttribute()
    {
        return $this->user ? $this->user->full_name : null;
    }
}
