<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // user who uploaded the file
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
