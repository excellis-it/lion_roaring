<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningEcomFooterCms extends BaseModel
{
    use HasFactory;


    protected $fillable = [
        'country_code',
        'footer_logo',
        'footer_title',
        'footer_newsletter_title',
        'footer_address_title',
        'footer_address',
        'footer_phone_number',
        'footer_email',
        'footer_copywrite_text',
        'footer_facebook_link',
        'footer_twitter_link',
        'footer_instagram_link',
        'footer_youtube_link',
    ];
}
