<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'first_name',
        'last_name',
        'email',
        'address',
        'city',
        'state',
        'postcode',
        'phone',
        'transaction_id',
        'donation_type',
        'donation_amount',
        'currency',
        'payment_method',
        'payment_status',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state');
    }

}
