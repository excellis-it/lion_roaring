<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EcomCmsPage;
use App\Models\EcomFooterCms;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
use App\Models\MemberPrivacyPolicy;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    use ImageTrait;
    public function memberPrivacyPolicy()
    {
        $policy = MemberPrivacyPolicy::orderBy('id', 'desc')->first();
        return view('user.cms.member_privacy_policy')->with('policy', $policy);
    }

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
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $count['pages'] = EcomCmsPage::count() + 2;
            $count['newsletter'] = EcomNewsletter::count();
            return view('user.cms.dashboard')->with('count', $count);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function list()
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $pages = EcomCmsPage::get();
            return view('user.cms.list')->with('pages', $pages);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function cms($page)
    {

        if (auth()->user()->hasRole('SUPER ADMIN')) {
            if ($page == 'home') {
                $cms = EcomHomeCms::orderBy('id', 'desc')->first();
                return view('user.cms.home_cms')->with('cms', $cms);
            } elseif ($page == 'footer') {
                $cms = EcomFooterCms::orderBy('id', 'desc')->first();
                return view('user.cms.footer_cms')->with('cms', $cms);
            } else {
                $cms = EcomCmsPage::where('slug', $page)->first();
                return view('user.cms.cms')->with('cms', $cms);
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function homeCmsUpdate(Request $request)
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
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
                $cms = EcomHomeCms::find($request->id);
                $message = 'Home CMS updated successfully';
            } else {
                $cms = new EcomHomeCms();
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

            $cms->save();
            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }




    public function footerUpdate(Request $request)
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
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
                $cms = EcomFooterCms::find($request->id);
                $message = 'Footer CMS updated successfully';
            } else {
                $cms = new EcomFooterCms();
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

            $cms->save();
            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create()
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            return view('user.cms.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function store(Request $request)
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'page_banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'slug' => 'required|string|unique:ecom_cms_pages,slug',
            ]);

            $cms = new EcomCmsPage();
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms');
            }

            $cms->save();
            return redirect()->route('user.cms.list')->with('message', 'CMS page added successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    public function update(Request $request, $id)
    {
        // dd($id);
        if (auth()->user()->hasRole('SUPER ADMIN')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'slug' => 'required|string|unique:ecom_cms_pages,slug,' . $id,
            ]);

            $cms = EcomCmsPage::find($id);
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms');
            }

            $cms->save();
            return redirect()->route('user.cms.list')->with('message', 'CMS page updated successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function delete($id)
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $cms = EcomCmsPage::find($id);
            $cms->delete();
            return redirect()->back()->with('message', 'CMS page deleted successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
