<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CreateSlug
{
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function createSlug($product_name)
    {

        if ($product_name) {
            $slug = str_replace(" ", "-", $product_name);
            $slug = strtolower($slug);
            return $slug;
        }
    }
}
