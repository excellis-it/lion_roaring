<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends BaseModel
{
    use HasFactory;

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
