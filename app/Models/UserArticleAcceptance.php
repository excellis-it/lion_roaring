<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserArticleAcceptance extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'country_code',
        'checkbox_text_snapshot',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
