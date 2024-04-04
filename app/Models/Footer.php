<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_logo',
        'footer_title',
        'footer_playstore_link',
        'footer_appstore_link',
        'footer_newsletter_title',
    ];
}
