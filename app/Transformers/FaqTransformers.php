<?php

namespace App\Transformers;

use App\Models\Faq;
use League\Fractal\TransformerAbstract;

class FaqTransformers extends TransformerAbstract
{

    public function transform(Faq $faq)
    {
        return [
            'title' => $faq->question,
            'content' => $faq->answer,
        ];
    }
}
