<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'country_id',
        'job_title',
        'job_description',
        'job_type',
        'job_location',
        'job_salary',
        'job_experience',
        'contact_person',
        'contact_email',
        'list_of_values',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
