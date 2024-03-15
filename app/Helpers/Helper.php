<?php

namespace App\Helpers;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function getPDFAttribute()
    {
        $article = Article::orderBy('id', 'desc')->first();
        if ($article) {
            return Storage::url($article->pdf);
        } else {
            return '';
        }
    }
}
