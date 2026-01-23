<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegisterAgreement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_code',
        'signer_name',
        'signer_initials',
        'pdf_path',
        'agreement_title_snapshot',
        'agreement_description_snapshot',
        'checkbox_text_snapshot',
    ];
}
