<?php

use App\Helpers\Helper;
use Illuminate\Support\HtmlString;

if (! function_exists('no_translate')) {
    /**
     * Wrap text so Google Website Translator leaves it unchanged (e.g. person names).
     */
    function no_translate(mixed $text): HtmlString
    {
        return Helper::noTranslate($text);
    }
}
