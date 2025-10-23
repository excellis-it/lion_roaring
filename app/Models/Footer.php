<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCountryCms;

class Footer extends Model
{
    use HasFactory, HasCountryCms;

    protected $fillable = [
        'footer_logo',
        'footer_title',
        'footer_playstore_link',
        'footer_playstore_icon',
        'footer_appstore_link',
        'footer_appstore_icon',
        'footer_newsletter_title',
        'footer_address_title',
        'footer_address',
        'footer_phone_number',
        'footer_email',
        'footer_copywrite_text',
    ];
}
