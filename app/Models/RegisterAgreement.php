<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_title',
        'agreement_description'
    ];

}
