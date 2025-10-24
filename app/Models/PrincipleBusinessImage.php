<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrincipleBusinessImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function principle()
    {
        return $this->belongsTo(PrincipalAndBusiness::class, 'principle_id', 'id');
    }
}
