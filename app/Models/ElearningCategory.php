<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status', 'image', 'meta_title', 'meta_description', 'main'];
}
