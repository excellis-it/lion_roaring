<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ImageTrait
{
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function imageUpload($file, $path)
    {

        if ($file) {
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $image_path = $file->store($path, 'public');

            return $image_path;
        }
    }
}
