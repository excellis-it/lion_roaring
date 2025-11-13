<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'flag_image',
        'status',
    ];

    /**
     * Get the languages associated with the country.
     */
    public function languages()
    {
        return $this->belongsToMany(TranslateLanguage::class, 'country_translate_language', 'country_id', 'translate_language_id');
    }
}
