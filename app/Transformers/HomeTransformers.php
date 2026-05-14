<?php

namespace App\Transformers;

use App\Helpers\Helper;
use App\Models\HomeCms;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class HomeTransformers extends TransformerAbstract
{

    public function transform(HomeCms $home)
    {
        $governances = Helper::getVisitorCmsContent('OurGovernance', false, false, 'order_no', 'asc', null);
        $organizations = Helper::getVisitorCmsContent('OurOrganization', false, false, 'id', 'desc', null);
        $testimonials = Helper::getVisitorCmsContent('Testimonial', false, false, 'id', 'desc', null);
        $details = Helper::getVisitorCmsContent('Detail', false, false, 'id', 'asc', null);
        $galleries = Helper::getVisitorCmsContent('Gallery', false, false, 'id', 'desc', null);

        return [
            'banner_title' => $home->banner_title ?? null,
            'banner_image' => $home->banner_image ? Storage::url($home->banner_image) : null,
            'banner_video' => $home->banner_video ? Storage::url($home->banner_video) : null,

            'section_1_title' => $home->section_1_title ?? null,
            'section_1_sub_title' => $home->section_1_sub_title ?? null,
            'section_1_description' => $home->section_1_description ?? null,
            'section_1_video' => $home->section_1_video ?? null,

            'section_2_title_1' => $home->section_2_left_title ?? null,
            'section_2_description_1' => $home->section_2_left_description ?? null,
            'section_2_image_1' => $home->section_2_left_image ? Storage::url($home->section_2_left_image) : null,
            'section_2_title_2' => $home->section_2_right_title ?? null,
            'section_2_description_2' => $home->section_2_right_description ?? null,
            'section_2_image_2' => $home->section_2_right_image ? Storage::url($home->section_2_right_image) : null,

            'section_3_title' => $home->section_3_title ?? null,
            'section_3_description' => $home->section_3_description ?? null,

            'section_4_title' => $home->section_4_title ?? null,
            'section_4_description' => $home->section_4_description ?? null,

            'section_5_title' => $home->section_5_title ?? null,

            'section_6_title' => $home->section_6_title ?? null,
            'section_6_subtitle' => $home->section_6_subtitle ?? null,
            'section_6_button_text' => $home->section_6_button_text ?? null,
            'section_6_button_link' => $home->section_6_button_link ?? null,

            'our_governance' => $governances->map(function ($governance) {
                return [
                    'slug' => $governance->slug,
                    'name' => $governance->name,
                    'description' => $governance->description,
                    'image' => $governance->image ? Storage::url($governance->image) : null,
                ];
            })->values(),

            'our_organization' => $organizations->map(function ($organization) {
                return [
                    'slug' => $organization->slug,
                    'name' => $organization->name,
                    'image' => $organization->image ? Storage::url($organization->image) : null,
                    'description' => $organization->description,
                ];
            })->values(),

            'testimonial' => $testimonials->map(function ($testimonial) {
                return [
                    'image' => $testimonial->image ? Storage::url($testimonial->image) : null,
                    'name' => $testimonial->name,
                    'address' => $testimonial->address,
                    'description' => $testimonial->description,
                ];
            })->values(),

            'details' => $details->map(function ($detail) {
                return [
                    'image' => $detail->image ? Storage::url($detail->image) : null,
                    'description' => $detail->description ?? null,
                ];
            })->values(),

            'gallery' => $galleries->map(function ($gallery) {
                return [
                    'image' => $gallery->image ? Storage::url($gallery->image) : null,
                ];
            })->values(),
        ];
    }
}
