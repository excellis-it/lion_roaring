<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_path',
        'compressed_path',
    ];
}
