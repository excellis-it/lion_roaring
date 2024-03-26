<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurOrganization extends Model
{
    use HasFactory;

    protected $fillable = [
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
