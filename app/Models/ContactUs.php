<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends BaseModel
{
    use HasFactory;

    protected $table = 'contact_us';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'message'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
