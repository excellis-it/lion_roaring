<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipMeasurement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'label',
        'description',
        'yearly_dues',
        'membership_card_title',
        'renewal_reminder_days',
        'renewal_reminder_subject',
        'renewal_reminder_body',
        'post_expiry_reminder_subject',
        'post_expiry_reminder_body',
    ];
}
