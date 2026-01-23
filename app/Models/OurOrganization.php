<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurOrganization extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'name',
        'slug',
        'image',
        'content',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
