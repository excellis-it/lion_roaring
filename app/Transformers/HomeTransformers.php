<?php

namespace App\Transformers;

use App\Models\Gallery;
use App\Models\HomeCms;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class HomeTransformers extends TransformerAbstract
{

    public function transform(HomeCms $home)
    {
        return [
            'banner_title' => $home->banner_title ?? null,
            'banner_image' => Storage::url($home->banner_image) ?? null,
            'section_1_title' => $home->section_1_title ?? null,
            'section_1_sub_title' => $home->section_1_sub_title ?? null,
            'section_1_description' => $home->section_1_description ?? null,
            'section_1_video' => $home->section_1_video ?? null,
            'section_2_title_1' => $home->section_2_left_title ?? null,
            'section_2_description_1' => $home->section_2_left_description ?? null,
            'section_2_image_1' => Storage::url($home->section_2_left_image) ?? null,
            'section_2_title_2' => $home->section_2_right_title ?? null,
            'section_2_description_2' => $home->section_2_right_description ?? null,
            'section_2_image_2' => Storage::url($home->section_2_right_image) ?? null,
            'section_3_title' => $home->section_3_title ?? null,
            'section_3_description' => $home->section_3_description ?? null,
            'section_4_title' => $home->section_4_title ?? null,
            'section_4_description' => $home->section_4_description ?? null,
            'section_5_title' => $home->section_5_title ?? null,
            'our_governance' => OurGovernance::orderBy('id', 'desc')->get()->map(function ($governance) {
                return [
                    'slug' => $governance->slug,
                    'name' => $governance->name,
                    'image' => Storage::url($governance->image)
                ];
            }),

            'our_organization' => OurOrganization::orderBy('id', 'desc')->get()->map(function ($organization) {
                return [
                    'slug' => $organization->slug,
                    'name' => $organization->name,
                    'image' => Storage::url($organization->image),
                    'description' => $organization->description,
                ];
            }),

            'testimonial' => Testimonial::orderBy('id', 'desc')->get()->map(function ($testimonial) {
                return [
                    'image' => Storage::url($testimonial->image),
                    'name' => $testimonial->name,
                    'address' => $testimonial->address,
                    'description' => $testimonial->description,
                ];
            }),

            'gallery' => Gallery::orderBy('id', 'desc')->get()->map(function ($gallery) {
                return [
                    'image' => Storage::url($gallery->image),
                ];
            }),
        ];
    }
}
