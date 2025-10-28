<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomContactCms extends Model
{
    use HasFactory;

    protected $table = 'ecom_contact_cms';

    protected $fillable = [
        'country_code',
        'banner_image',
        'banner_title',
        'card_one_title',
        'card_one_content',
        'card_two_title',
        'card_two_content',
        'card_three_title',
        'card_three_content',
        'form_title',
        'form_subtitle',
        'call_section_title',
        'call_section_content',
        'follow_us_title',
        'map_iframe_src',
    ];
}
