<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslateLanguage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the countries associated with the language.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_translate_language', 'translate_language_id', 'country_id');
    }
}
