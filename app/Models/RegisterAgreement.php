<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterAgreement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'agreement_title',
        'agreement_description',
        'checkbox_text',
    ];
}
