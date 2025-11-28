<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipMeasurement extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'description', 'yearly_dues'];
}
