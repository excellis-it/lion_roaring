<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSocialLink extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'class',
        'url',
    ];
    
}
