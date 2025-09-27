<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\Country;
use App\Models\EcomCmsPage;
use App\Models\EcomFooterCms;
use App\Models\EcomHomeCms;
use App\Models\ElearningEcomCmsPage;
use App\Models\ElearningEcomFooterCms;
use App\Models\Footer;
use App\Models\MailUser;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\OurOrganization;
use App\Models\PmaTerm;
use App\Models\RegisterAgreement;
use App\Models\Review;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Constraint\Count;
use App\Models\User;
use App\Models\SiteSetting;
use GuzzleHttp\Client;
use App\Models\WareHouse;
use App\Models\EstoreCart;
use Illuminate\Support\Facades\Route;

class Helper
{
    public static function renderCategoryTree($categories = null)
    {
        // Root categories if not passed
        if ($categories === null) {
            $categories = Category::whereNull('parent_id')
                ->with('children')
                ->get();
        }

        if ($categories->isEmpty()) {
            return '';
        }

        $html = '<ul class="dropdown-menu">'; // dropdown UL

        foreach ($categories as $category) {
            $routeName = $category->slug . '.page';

            $html .= '<li class="dropdown-item">';
            if (Route::has($routeName)) {
                $html .= '<a href="' . route($routeName) . '">' . e($category->name) . '</a>';
            } else {
                $html .= e($category->name);
            }

            // If has children → recursive dropdown
            if ($category->children && $category->children->count() > 0) {
                $html .= self::renderCategoryTree($category->children);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    // app/Helpers/Helper.php
    public static function renderBreadcrumbs($category = null)
    {
        $breadcrumbs = [];

        // Home is always first
        $breadcrumbs[] = ['name' => 'Home', 'url' => route('home')];

        if ($category) {
            // Traverse up the category tree to root
            $current = $category;
            $stack = [];
            while ($current) {
                $stack[] = [
                    'name' => $current->name,
                    'url'  => route($current->slug . '.page') // assuming dynamic route
                ];
                $current = $current->parent; // Make sure Category model has parent() relationship
            }

            // Reverse to get root -> child order
            $breadcrumbs = array_merge($breadcrumbs, array_reverse($stack));
        }

        // Generate HTML
        $html = '<ol class="cd-breadcrumb custom-separator">';
        $lastIndex = count($breadcrumbs) - 1;

        foreach ($breadcrumbs as $index => $crumb) {
            $class = $index === $lastIndex ? 'current' : '';
            $html .= '<li class="' . $class . '">';
            $html .= '<a href="' . $crumb['url'] . '">' . e($crumb['name']) . '</a>';
            $html .= '</li>';
        }

        $html .= '</ol>';

        return $html;
    }




    public static function getPDFAttribute()
    {
        $article = Article::orderBy('id', 'desc')->first();
        if ($article) {
            return Storage::url($article->pdf);
        } else {
            return '';
        }
    }

    public static function getOrganzations()
    {
        $organizations = OurOrganization::orderBy('id', 'desc')->get();
        return $organizations;
    }

    public static function getSettings()
    {
        $settings = SiteSetting::orderBy('id', 'desc')->first();
        return $settings;
    }

    public static function getCountries()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return $countries;
    }

    public static function getFooter()
    {
        $footer = Footer::orderBy('id', 'desc')->first();
        return $footer;
    }

    public static function expireTo($date)
    {
        // how many day left to expire
        $now = time();
        $your_date = strtotime($date);
        $datediff = $your_date - $now;
        $days = floor($datediff / (60 * 60 * 24));
        return $days;
    }

    public static function showTheLastChat($sender_id, $reciver_id)
    {
        $chats = Chat::where(function ($query) use ($sender_id, $reciver_id) {
            $query->where('sender_id', $sender_id)
                ->where('reciver_id', $reciver_id);
        })
            ->orWhere(function ($query) use ($sender_id, $reciver_id) {
                $query->where('sender_id', $reciver_id)
                    ->where('reciver_id', $sender_id);
            })
            ->orderBy('created_at', 'desc')
            ->first();
        return $chats;
    }

    public static function getPmaTerm()
    {
        $term = PmaTerm::orderBy('id', 'desc')->first();
        return $term;
    }

    public static function getTotalProductRating($product_id)
    {
        $total_rating = Review::where('product_id', $product_id)->sum('rating');
        $total_review = Review::where('product_id', $product_id)->count();
        if ($total_review > 0) {
            $avg_rating = $total_rating / $total_review;
        } else {
            $avg_rating = 0;
        }
        // showing 1 decimal point
        return $avg_rating = number_format((float)$avg_rating, 1, '.', '');
    }

    public static function getRatingCount($product_id)
    {
        $rating_count = Review::where('product_id', $product_id)->count();
        return $rating_count;
    }

    public static function getCmsPages()
    {
        $pages = EcomCmsPage::where('id', '<', 3)->get();
        return $pages;
    }

    public static function getFooterCms()
    {
        $cms = EcomFooterCms::orderBy('id', 'desc')->first();
        return $cms;
    }

    public static function getElearningCmsPages()
    {
        $pages = ElearningEcomCmsPage::get();
        return $pages;
    }

    public static function getElearningFooterCms()
    {
        $cms = ElearningEcomFooterCms::orderBy('id', 'desc')->first();
        return $cms;
    }

    public static function getAgreements()
    {
        $agreement = RegisterAgreement::orderBy('id', 'desc')->first();
        return $agreement;
    }

    public static function checkAdminTeam($user_id, $team_id)
    {
        $team = Team::where('id', $team_id)->whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('is_admin', true)->where('is_removed', false);
        })->first();
        if ($team) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkRemovedFromTeam($team_id, $user_id)
    {
        $team_member_check = TeamMember::where('team_id', $team_id)->where('user_id', $user_id)->first();

        if ($team_member_check->is_removed == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function userLastMessage($team_id, $user_id)
    {
        return TeamChat::where('team_id', $team_id)->whereHas('chatMembers', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->latest()->first();
    }

    public static function checkMemberInTeam($team_id, $user_id)
    {
        $team_member_check = TeamMember::where(function ($query) use ($team_id, $user_id) {
            $query->where('team_id', $team_id)
                ->where('user_id', $user_id)
                ->where('is_removed', false);
        })->first();

        if ($team_member_check) {
            return true;
        } else {
            return false;
        }
    }

    public static function getCountUnseenMessage($sender_id, $reciver_id)
    {
        $chats = Chat::where('reciver_id', $sender_id)
            ->where('sender_id', $reciver_id)
            ->where('seen', 0)
            ->where('delete_from_receiver_id', 0)
            ->count();
        return $chats;
    }

    public static function notificationCount()
    {
        if (auth()->check()) {
            $notifications = Notification::where('user_id', auth()->user()->id)->where('is_read', 0)->where('is_delete', 0)->count();
            return $notifications;
        } else {
            return 0;
        }
    }

    public static function getTeamCountUnseenMessage($user_id, $team_id)
    {
        $team_chat = ChatMember::where('user_id', $user_id)
            ->whereHas('chat', function ($query) use ($team_id) {
                $query->where('team_id', $team_id);
            })
            ->where('is_seen', 0)
            ->count();
        return $team_chat;
    }

    public static function isOwner($id)
    {
        if (auth()->check()) {
            $team = Team::where('id', $id)->where('user_id', auth()->user()->id)->first();
            if ($team) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getMailTo($mail_id)
    {
        $mail_to = MailUser::where('send_mail_id', $mail_id)->where('is_to', 1)->get();

        $to = [];
        foreach ($mail_to as $mail) {
            if (!empty($mail->user->full_name)) {
                $to[] = $mail->user->full_name;
            }
        }

        return implode(', ', $to);
    }

    public static function format_links_in_message($message)
    {
        return preg_replace_callback(
            '/\b((http|https|ftp|ftps):\/\/\S+|www\.\S+)/i',
            function ($matches) {
                $url = $matches[0];

                // If the URL starts with 'www', prepend 'http://' to make it a valid URL
                if (strpos($url, 'www.') === 0) {
                    $url = 'http://' . $url;
                }

                // Check if the URL is already inside an <a> tag and skip it
                if (strpos($url, '<a href=') === false) {
                    return '<a class="text-decoration-underline" href="' . $url . '" target="_blank">' . $url . '</a>';
                }

                return $url; // Return the URL as-is if it's already in an <a> tag
            },
            // Clean any stray closing HTML tags attached to URLs and fix spacing
            preg_replace(
                '/<a[^>]+>(.*?)<\/a>/i',
                '$1',
                preg_replace('/(\S)(<\/?[^>]+>)/', '$1 $2', $message)
            )
        );
    }

    public static function formatChatMessage($message)
    {
        // // Regular expression to match words containing a dot (.)
        // $pattern = '/\b[a-zA-Z0-9._-]+\.[a-zA-Z]{2,}\b/';

        // // Replace matched words with anchor tags
        // $formattedMessage = preg_replace_callback($pattern, function ($matches) {
        //     $url = $matches[0];
        //     return '<a class="text-decoration-underline" href="https://' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a>';
        // }, $message);

        return nl2br($message);
    }

    public static function formatChatSendMessage($message)
    {
        // Regular expression to match full URLs with protocols and without protocols
        $pattern = '/\b((https?|ftp):\/\/[^\s<>"]+|www\.[^\s<>"]+|[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}[^\s<>"]*)/i';

        // Replace matched URLs with anchor tags
        $formattedMessage = preg_replace_callback($pattern, function ($matches) {
            $url = $matches[0];

            // If URL doesn't start with protocol, add https://
            if (!preg_match('/^https?:\/\//i', $url)) {
                $href = 'https://' . $url;
            } else {
                $href = $url;
            }

            return '<a class="text-decoration-underline" href="' . htmlspecialchars($href) . '" target="_blank">' . htmlspecialchars($url) . '</a>';
        }, $message);

        return $formattedMessage;
    }

    public static function unreadMessagesCount(string $fcmtoken)
    {
        $user = User::where('fcm_token', $fcmtoken)->first();
        if (!$user) {
            return 0; // or throw an exception
        }

        // Count unread emails where user is recipient and email is not deleted
        $mailCount = \App\Models\MailUser::where('user_id', $user->id)
            ->where('is_read', 0)
            ->where('is_delete', 0)
            ->count();

        // Count unread individual chats where user is receiver
        $chatCount = \App\Models\Chat::where('reciver_id', $user->id)
            ->where('seen', 0)
            ->where('deleted_for_reciver', 0)
            ->where('delete_from_receiver_id', 0)
            ->count();

        // Count unread team chat messages where user is a member
        $teamChatCount = \App\Models\ChatMember::where('user_id', $user->id)
            ->where('is_seen', 0)
            ->whereHas('chat', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->count();

        $totalCount = $mailCount + $chatCount + $teamChatCount;

        return $totalCount;
    }

    function getDistance($originLat, $originLng, $destLat, $destLng)
    {
        try {
            $client = new Client();
            $apiKey = env('GOOGLE_MAPS_API_KEY'); // store API key in .env

            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$originLat},{$originLng}&destinations={$destLat},{$destLng}&key={$apiKey}";

            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            if (!empty($data['rows'][0]['elements'][0]['distance']['value'])) {
                // distance in meters, convert to KM
                $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                $distanceKm = $distanceMeters / 1000;
                return $distanceKm;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculate Haversine distance between two points in kilometers.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float distance in kilometers
     */
    public static function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Return nearest warehouse model and distance in km.
     *
     * @param float|null $originLat
     * @param float|null $originLng
     * @param bool $onlyActive
     * @param bool $respectServiceRange
     * @return array|null ['warehouse' => WareHouse, 'distance_km' => float] or null
     */
    public static function getNearestWarehouse($originLat, $originLng, $onlyActive = true, $respectServiceRange = true)
    {
        if (empty($originLat) || empty($originLng)) {
            return null;
        }

        $query = WareHouse::query();
        if ($onlyActive) {
            $query->where('is_active', 1);
        }

        $warehouses = $query->get();
        $minDistance = null;
        $nearest = null;

        foreach ($warehouses as $wh) {
            if ($wh->location_lat === null || $wh->location_lng === null) {
                continue;
            }

            // if warehouse have no warehouseProducts, skip and go to next warehouse
            if ($wh->warehouseProducts()->count() == 0) {
                continue;
            }

            $distance = self::haversineDistance($originLat, $originLng, $wh->location_lat, $wh->location_lng);

            $isAuth = auth()->check();
            $isUser = auth()->user();
            if ($isUser) {
                $user_location_country_name = $isUser->location_country ?? null;
            } else {
                $user_location_country_name = session('location_country') ?? null;
            }

            // $user_location_country_name = auth()->check() && auth()->user()->location_country ? auth()->user()->location_country : null;
            $warehouses_location_country_name = $wh->country ? $wh->country->name : null;

            if ($user_location_country_name && $warehouses_location_country_name && $user_location_country_name == $warehouses_location_country_name) {

                if (is_null($minDistance) || $distance < $minDistance) {
                    // if respecting service_range, ensure warehouse is within its service_range (if set)
                    if ($respectServiceRange && !is_null($wh->service_range) && $distance > $wh->service_range) {
                        // skip — out of range
                        continue;
                    }

                    $minDistance = $distance;
                    $nearest = $wh;
                }
            }
        }

        if ($nearest) {
            return ['warehouse' => $nearest, 'distance_km' => $minDistance];
        }

        return null;
    }

    /**
     * Convenience: return nearest warehouse id or provided default.
     *
     * @param float|null $originLat
     * @param float|null $originLng
     * @param int|null $defaultId
     * @return int|null
     */
    public static function getNearestWarehouseId($originLat, $originLng, $defaultId = null)
    {
        $result = self::getNearestWarehouse($originLat, $originLng);
        if ($result && isset($result['warehouse']->id)) {
            return $result['warehouse']->id;
        }
        return $defaultId;
    }

    // cartCount
    public static function cartCount()
    {
        if (auth()->check()) {
            $cartCount = EstoreCart::where('user_id', auth()->user()->id)->count();
            return $cartCount;
        } else {
            return 0;
        }
    }

    // getCurrencyFormat
    public static function getCurrencyFormat($amount, $currencySymbol = '$')
    {
        return $currencySymbol . number_format($amount, 2);
    }

    /**
     * Resolve the banner image URL for e-store pages.
     * Priority:
     * 1) EcomCmsPage by slug ($pageKey) -> page_banner_image
     * 2) EcomHomeCms latest -> banner_image
     * 3) Provided $defaultAsset (public asset path)
     *
     * Example usage in blade:
     * style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('cart') }})"
     */
    public static function estorePageBannerUrl(?string $pageKey = null, string $defaultAsset = 'ecom_assets/images/slider-bg.png'): string
    {
        // Try CMS page specific banner by slug
        if ($pageKey) {
            $path = EcomCmsPage::where('slug', $pageKey)->value('page_banner_image');
            if ($path) {
                return Storage::url($path);
            }
        }

        // Fallback to Home CMS banner if available
        $homeBanner = EcomHomeCms::orderByDesc('id')->value('banner_image');
        if ($homeBanner) {
            return Storage::url($homeBanner);
        }

        // Final fallback to static asset
        return asset($defaultAsset);
    }

    // estore header logo
    public static function estoreHeaderLogoUrl(string $defaultAsset = 'ecom_assets/images/estore_logo.png'): string
    {
        $headerLogo = EcomHomeCms::orderByDesc('id')->value('header_logo');
        if ($headerLogo) {
            return Storage::url($headerLogo);
        }

        // Final fallback to static asset
        return asset($defaultAsset);
    }
}
