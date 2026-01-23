<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'topic_name',
        'education_type',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
