<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ElearningEcomCmsPage;
use App\Models\ElearningEcomFooterCms;
use App\Models\ElearningEcomHomeCms;
use App\Models\ElearningEcomNewsletter;
use App\Models\MemberPrivacyPolicy;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ElearningCmsController extends Controller
{
    use ImageTrait;
    // public function memberPrivacyPolicy()
    // {
    //     $policy = MemberPrivacyPolicy::orderBy('id', 'desc')->first();
    //     return view('user.elearning-cms.member_privacy_policy')->with('policy', $policy);
    // }

    public function page($name, $permission)
    {
        $permission = $permission;
        if (auth()->user()->can($permission)) {
            $name = $name;
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
        return view('user.cms')->with(compact('name', 'permission'));
    }

    public function dashboard()
    {
        if (auth()->user()->can('Manage Elearning CMS')) {
            $count['pages'] = ElearningEcomCmsPage::count() + 2;
            $count['newsletter'] = ElearningEcomNewsletter::count();
            return view('user.elearning-cms.dashboard')->with('count', $count);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function list()
    {
        if (auth()->user()->can('Manage Elearning CMS')) {
            $pages = ElearningEcomCmsPage::get();
            return view('user.elearning-cms.list')->with('pages', $pages);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function cms($page, Request $request)
    {

        if (auth()->user()->can('View Elearning CMS')) {
            if ($page == 'home') {
                $cms = ElearningEcomHomeCms::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
                return view('user.elearning-cms.home_cms')->with('cms', $cms);
            } elseif ($page == 'footer') {
                $cms = ElearningEcomFooterCms::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
                return view('user.elearning-cms.footer_cms')->with('cms', $cms);
            } else {
                $cms = ElearningEcomCmsPage::where('slug', $page)->first();
                return view('user.elearning-cms.cms')->with('cms', $cms);
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function homeCmsUpdate(Request $request)
    {
        if (auth()->user()->can('Edit Elearning CMS')) {
            $request->validate([
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'banner_title' => 'required|string',
                'banner_subtitle' => 'required|string',
                'product_category_title' => 'required|string',
                'product_category_subtitle' => 'required|string',
                'featured_product_title' => 'required|string',
                'featured_product_subtitle' => 'required|string',
                'new_product_title' => 'required|string',
                'new_product_subtitle' => 'required|string',
            ]);

            if ($request->id) {
                $cms = ElearningEcomHomeCms::find($request->id);
                $message = 'Home CMS updated successfully';
            } else {
                $cms = new ElearningEcomHomeCms();
                $message = 'Home CMS added successfully';
            }

            $cms->banner_title = $request->banner_title;
            $cms->banner_subtitle = $request->banner_subtitle;
            $cms->product_category_title = $request->product_category_title;
            $cms->product_category_subtitle = $request->product_category_subtitle;
            $cms->featured_product_title = $request->featured_product_title;
            $cms->featured_product_subtitle = $request->featured_product_subtitle;
            $cms->new_product_title = $request->new_product_title;
            $cms->new_product_subtitle = $request->new_product_subtitle;
            if ($request->hasFile('banner_image')) {
                $cms->banner_image = $this->imageUpload($request->file('banner_image'), 'ecom_cms');
            }

            // $cms->save();
            $country = $request->content_country_code ?? 'US';
            $cms = ElearningEcomHomeCms::updateOrCreate(['country_code' => $country], array_merge($cms->getAttributes(), ['country_code' => $country]));
            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }




    public function footerUpdate(Request $request)
    {
       // return request()->all();
        if (auth()->user()->can('Edit Elearning CMS')) {
            $request->validate([
                'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'footer_title' => 'required|string',
                'footer_newsletter_title' => 'required|string',
                'footer_address_title' => 'required|string',
                'footer_address' => 'required|string',
                'footer_phone_number' => 'required|string',
                'footer_email' => 'required|string',
                'footer_copywrite_text' => 'required|string',
                'footer_facebook_link' => 'nullable|string',
                'footer_twitter_link' => 'nullable|string',
                'footer_instagram_link' => 'nullable|string',
                'footer_youtube_link' => 'nullable|string',
            ]);

            if ($request->id) {
                $cms = ElearningEcomFooterCms::find($request->id);
                $message = 'Footer CMS updated successfully';
            } else {
                $cms = new ElearningEcomFooterCms();
                $message = 'Footer CMS added successfully';
            }

            $cms->footer_title = $request->footer_title;
            $cms->footer_newsletter_title = $request->footer_newsletter_title;
            $cms->footer_address_title = $request->footer_address_title;
            $cms->footer_address = $request->footer_address;
            $cms->footer_phone_number = $request->footer_phone_number;
            $cms->footer_email = $request->footer_email;
            $cms->footer_copywrite_text = $request->footer_copywrite_text;
            $cms->footer_facebook_link = $request->footer_facebook_link;
            $cms->footer_twitter_link = $request->footer_twitter_link;
            $cms->footer_instagram_link = $request->footer_instagram_link;
            $cms->footer_youtube_link = $request->footer_youtube_link;
            if ($request->hasFile('footer_logo')) {
                $cms->footer_logo = $this->imageUpload($request->file('footer_logo'), 'ecom_cms');
            }

            // $cms->save();
            $country = $request->content_country_code ?? 'US';
            $cms = ElearningEcomFooterCms::updateOrCreate(['country_code' => $country], array_merge($cms->getAttributes(), ['country_code' => $country]));
            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create()
    {
        if (auth()->user()->can('Create Elearning CMS')) {
            return view('user.elearning-cms.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function store(Request $request)
    {
        if (auth()->user()->can('Create Elearning CMS')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'page_banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'slug' => 'required|string|unique:ecom_cms_pages,slug',
            ]);

            $cms = new ElearningEcomCmsPage();
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms');
            }

            $cms->save();
            return redirect()->route('user.elearning-cms.list')->with('message', 'CMS page added successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    public function update(Request $request, $id)
    {
        // dd($id);
        if (auth()->user()->can('Edit Elearning CMS')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'slug' => 'required|string|unique:ecom_cms_pages,slug,' . $id,
            ]);

            $cms = ElearningEcomCmsPage::find($id);
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms');
            }

            $cms->save();
            return redirect()->route('user.elearning-cms.list')->with('message', 'CMS page updated successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function delete($id)
    {
        if (auth()->user()->can('Delete Elearning CMS')) {
            $cms = ElearningEcomCmsPage::find($id);
            $cms->delete();
            return redirect()->back()->with('message', 'CMS page deleted successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function cmsPage($page_id = null)
    {
        $page_id = $page_id ?? 1;
        $cms = ElearningEcomCmsPage::findOrfail($page_id);
        return view('elearning.cms')->with(compact('cms'));
    }
}
