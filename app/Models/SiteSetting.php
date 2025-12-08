<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'SITE_NAME',
        'SITE_LOGO',
        'PANEL_WATERMARK_LOGO',
        'SITE_CONTACT_EMAIL',
        'SITE_CONTACT_PHONE',
        'DONATE_TEXT',
        'DONATE_BANK_TRANSFER_DETAILS'
    ];
}
