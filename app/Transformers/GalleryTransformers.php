<?php

namespace App\Transformers;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class GalleryTransformers extends TransformerAbstract
{

    public function transform(Gallery $gallery)
    {
        return [
            'image_url' => Storage::url($gallery->image),
        ];
    }
}
