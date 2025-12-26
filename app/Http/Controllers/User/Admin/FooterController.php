<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Footer;
use App\Models\FooterSocialLink;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;


class FooterController extends Controller
{
    use ImageTrait;

    public $user_type;
    public $user_country;
    public $country;

    // use consructor
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user_type = auth()->user()->user_type;
            $this->user_country = auth()->user()->country;
            $this->country = Country::where('id', $this->user_country)->first();

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Footer')) {
            if ($this->user_type == 'Global') {
                $footer = Footer::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $footer = Footer::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            $social_links = FooterSocialLink::get();
            return view('user.admin.footer.update')->with(compact('footer', 'social_links'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request)
    {
        // return $request;
        $request->validate([
            'footer_title' => 'required',
            'footer_address_title' => 'required',
            'footer_address' => 'required',
            'footer_phone_number' => 'required',
            'footer_email' => 'required',
            'footer_copywrite_text' => 'required',
            'footer_newsletter_title' => 'required',
            'footer_playstore_link' => 'nullable',
            'footer_appstore_link' => 'nullable',
        ]);

        if ($request->id != '') {
            $footer = Footer::find($request->id);
        } else {
            $footer = new Footer();
        }

        $footer->footer_title = $request->footer_title;
        $footer->footer_newsletter_title = $request->footer_newsletter_title;
        $footer->footer_address_title = $request->footer_address_title;
        $footer->footer_address = $request->footer_address;
        $footer->footer_phone_number = $request->footer_phone_number;
        $footer->footer_email = $request->footer_email;
        $footer->footer_copywrite_text = $request->footer_copywrite_text;
        $footer->footer_playstore_link = $request->footer_playstore_link;
        $footer->footer_appstore_link = $request->footer_appstore_link;
        if ($request->hasFile('footer_logo')) {
            $request->validate([
                'footer_logo' => 'mimes:jpeg,jpg,png,gif,webp|required',
            ]);
            $footer->footer_logo = $this->imageUpload($request->file('footer_logo'), 'footer');
        }
        if ($request->hasFile('footer_playstore_icon')) {
            $request->validate([
                'footer_playstore_icon' => 'mimes:jpeg,jpg,png,gif|required',
            ]);
            $footer->footer_playstore_icon = $this->imageUpload($request->file('footer_playstore_icon'), 'footer');
        }
        if ($request->hasFile('footer_appstore_icon')) {
            $request->validate([
                'footer_appstore_icon' => 'mimes:jpeg,jpg,png,gif|required',
            ]);
            $footer->footer_appstore_icon = $this->imageUpload($request->file('footer_appstore_icon'), 'footer');
        }

        if ($request->hasFile('footer_flag')) {
            $request->validate([
                'footer_flag' => 'mimes:jpeg,jpg,png,gif|required',
            ]);
            $footer->footer_flag = $this->imageUpload($request->file('footer_flag'), 'footer');
        }



        //  $footer->save();
        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
        $footer = Footer::updateOrCreate(['country_code' => $country], array_merge($footer->getAttributes(), ['country_code' => $country]));



        if ($request->class) {
            FooterSocialLink::truncate();
            foreach ($request->class as $key => $class) {
                if ($class) {
                    $social_link = new FooterSocialLink();
                    $social_link->class = $class;
                    $social_link->url = $request->url[$key];
                    $social_link->save();
                }
            }
        }
        return redirect()->back()->with('message', 'Footer updated successfully');
    }
}
