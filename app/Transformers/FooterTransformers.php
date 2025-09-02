<?php

namespace App\Transformers;

use App\Models\Footer;
use App\Models\FooterSocialLink;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class FooterTransformers extends TransformerAbstract
{

    public function transform(Footer $footer)
    {
        return [
            'Footer_logo' => Storage::url($footer->footer_logo),
            'Footer_description' => $footer->footer_title,
            'Footer_social_section' => FooterSocialLink::pluck('url', 'class'),
            'Footer_address' => $footer->footer_address_title,
            'Footer_address_details' => $footer->footer_address,
            'Footer_phoneno' => $footer->footer_phone_number,
            'Footer_Emailid' => $footer->footer_email,
            'Footer_form_title' => $footer->footer_newsletter_title,
            'Footer_copywrite_text' => $footer->footer_copywrite_text,
        ];
    }
}
