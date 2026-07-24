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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Constraint\Count;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\MenuItem;
use GuzzleHttp\Client;
use App\Models\WareHouse;
use App\Models\EstoreCart;
use App\Models\GlobalImage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Helper
{
    private static $settingsCache = null;

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
        $breadcrumbs[] = ['name' => 'Home', 'url' => route('e-store')];

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
        // $article = Article::orderBy('id', 'desc')->first();
        $article = self::getVisitorCmsContent('Article', true, false, 'id', 'desc', null);
        if ($article) {
            return Storage::url($article->pdf);
        } else {
            return '';
        }
    }

    public static function getArticleCheckboxText()
    {
        $article = self::getVisitorCmsContent('Article', true, false, 'id', 'desc', null);
        if ($article && $article->checkbox_text) {
            return $article->checkbox_text;
        }
        return 'I have read and agree to the Articles of Association';
    }

    public static function getOrganzations()
    {
        $organizations = OurOrganization::orderBy('id', 'desc')->get();
        return $organizations;
    }

    public static function getSettings()
    {
        if (self::$settingsCache === null) {
            self::$settingsCache = SiteSetting::orderBy('id', 'desc')->first();
        }

        return self::$settingsCache;
    }

    public static function getCountries()
    {
        $countries = Country::with('languages')
            ->where('status', 1)
            ->where('is_global', false) // exclude GLOBAL entry from frontend dropdowns
            ->orderBy('name', 'asc')
            ->get();
        return $countries;
    }

    public static function getFooter()
    {
        // $footer = Footer::orderBy('id', 'desc')->first();
        $footer = self::getVisitorCmsContent('Footer', true, false, 'id', 'desc', null);


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
        // $term = PmaTerm::orderBy('id', 'desc')->first();
        $term = self::getVisitorCmsContent('PmaTerm', true, false, 'id', 'desc', null);
        return $term;
    }

    public static function getTotalProductRating($product_id)
    {
        $total_rating = Review::where('product_id', $product_id)->whereStatus(2)->sum('rating');
        $total_review = Review::where('product_id', $product_id)->whereStatus(2)->count();
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
        $rating_count = Review::where('product_id', $product_id)->where('status', 2)->count();
        return $rating_count;
    }

    public static function getCmsPages()
    {
        $pages = EcomCmsPage::where('id', '<', 3)->get();
        return $pages;
    }

    public static function getFooterCms()
    {
        //  $cms = EcomFooterCms::orderBy('id', 'desc')->first();
        $cms = self::getVisitorCmsContent('EcomFooterCms', true, false, 'id', 'desc', null);


        return $cms;
    }

    public static function getElearningCmsPages()
    {
        // $pages = ElearningEcomCmsPage::get();
        //  $pages = self::getVisitorCmsContent('ElearningEcomCmsPage', false, false, 'id', 'asc', null);
        $pages = ElearningEcomCmsPage::select('elearning_ecom_cms_pages.*')
            ->join(DB::raw('(SELECT MIN(id) as id FROM elearning_ecom_cms_pages GROUP BY slug) as unique_pages'), 'elearning_ecom_cms_pages.id', '=', 'unique_pages.id')
            ->orderBy('elearning_ecom_cms_pages.id', 'asc')
            ->get();
        return $pages;
    }

    public static function getElearningFooterCms()
    {
        // $cms = ElearningEcomFooterCms::orderBy('id', 'desc')->first();
        $cms = self::getVisitorCmsContent('ElearningEcomFooterCms', true, false, 'id', 'desc', null);
        return $cms;
    }

    public static function getAgreements()
    {
        // $agreement = RegisterAgreement::orderBy('id', 'desc')->first();
        $agreement = self::getVisitorCmsContent('RegisterAgreement', true, false, 'id', 'desc', null);
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
            ->where('deleted_for_reciver', 0)
            ->where('delete_from_receiver_id', 0)
            ->count();
        return $chats;
    }

    public static function notificationCount()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $authId = $user->id;

            // Total count for non-chat notifications
            $baseCount = Notification::where('user_id', $authId)
                ->where('is_read', 0)
                ->where('is_delete', 0)
                ->where('type', '!=', 'Chat')
                ->count();

            // Filtered count for chat notifications
            $chatNotificationCount = Notification::where('user_id', $authId)
                ->where('is_read', 0)
                ->where('is_delete', 0)
                ->where('type', 'Chat')
                ->whereHas('chat.sender', function ($query) use ($user) {
                    $query->where('status', 1)
                        ->whereHas('userRole', function ($q) {
                            $q->whereIn('type', [1, 2, 3]);
                        });

                    $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

                    if (!$isSuperAdmin) {
                        $user_type = $user->user_type;
                        $country_name = $user->country;
                        $authId = $user->id;

                        $query->where(function ($q) use ($user_type, $country_name, $authId) {
                            if ($user_type == 'Global') {
                                // Global user: see Global non-SA users + Super Admins who messaged me first
                                $q->where(function ($sq) {
                                    $sq->where('user_type', 'Global')
                                        ->whereDoesntHave('userRole', function ($r) {
                                            $r->where('name', 'SUPER ADMIN');
                                        });
                                })->orWhere(function ($sq) use ($authId) {
                                    $sq->whereHas('userRole', function ($r) {
                                        $r->where('name', 'SUPER ADMIN');
                                    })->whereHas('chatSender', function ($chat) use ($authId) {
                                        $chat->where('reciver_id', $authId);
                                    });
                                });
                            } else {
                                // Regional user: see same-country Regional non-SA users + Super Admins who messaged me first
                                $q->where(function ($sq) use ($country_name) {
                                    $sq->where('user_type', 'Regional')
                                        ->where('country', $country_name)
                                        ->whereDoesntHave('userRole', function ($r) {
                                            $r->where('name', 'SUPER ADMIN');
                                        });
                                })->orWhere(function ($sq) use ($authId) {
                                    $sq->whereHas('userRole', function ($r) {
                                        $r->where('name', 'SUPER ADMIN');
                                    })->whereHas('chatSender', function ($chat) use ($authId) {
                                        $chat->where('reciver_id', $authId);
                                    });
                                });
                            }
                        });
                    }
                })
                ->count();

            return $baseCount + $chatNotificationCount;
        } else {
            return 0;
        }
    }

    /**
     * Format a badge count for compact UI display (caps at 99+).
     */
    public static function formatBadgeCount(int $count): string
    {
        if ($count <= 0) {
            return '0';
        }

        return $count > 99 ? '99+' : (string) $count;
    }

    public static function getTeamCountUnseenMessage($user_id, $team_id)
    {
        $team_chat = ChatMember::where('user_id', $user_id)
            ->where('is_seen', 0)
            ->whereHas('chat', function ($query) use ($team_id) {
                $query->where('team_id', $team_id)
                    ->whereNull('deleted_at');
            })
            ->whereHas('chat.team.members', function ($query) use ($user_id) {
                // Check user is still a member and not removed
                $query->where('user_id', $user_id)
                    ->where('is_removed', false);
            })
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

    // Plain-text preview for notifications / chat-list subtitles: strip all HTML
    // tags and decode entities so a pasted <a href> shows its label, not markup.
    public static function chatPreviewText($message)
    {
        if ($message === null || $message === '') {
            return '';
        }
        $text = preg_replace('/<br\s*\/?>/i', ' ', $message);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    public static function formatChatMessage($message)
    {
        if ($message === null || $message === '') {
            return '';
        }

        // Give scheme-less www. links a scheme so they get linkified too (HTMLPurifier
        // only auto-links URLs that already have a scheme). Skip ones already in an href.
        $message = preg_replace('/(^|[\s>])(www\.[^\s<]+)/i', '$1https://$2', $message);

        // Build the purifier once per request — it is expensive to construct.
        static $purifier = null;
        if ($purifier === null) {
            $cachePath = storage_path('app/htmlpurifier');
            if (!is_dir($cachePath)) {
                @mkdir($cachePath, 0755, true);
            }

            $config = \HTMLPurifier_Config::createDefault();
            // Allow only safe tags; pasted <a> links survive, scripts/handlers/styles are stripped.
            $config->set('HTML.Allowed', 'a[href|title],b,strong,i,em,u,br,p,ul,ol,li');
            $config->set('AutoFormat.Linkify', true);   // turn bare URLs into links
            $config->set('HTML.TargetBlank', true);     // force target=_blank + rel=noreferrer
            $config->set('Cache.SerializerPath', $cachePath);

            $purifier = new \HTMLPurifier($config);
        }

        $clean = $purifier->purify($message);

        return nl2br($clean);
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
            ->where('is_to', 1)
            ->where('is_delete', 0)
            ->where('is_read', 0)
            ->count();

        // Count unread individual chats where user is receiver
        // AND sender is an active user with valid role
        $chatCount = \App\Models\Chat::where('reciver_id', $user->id)
            ->where('seen', 0)
            ->where('deleted_for_reciver', 0)
            ->where('delete_from_receiver_id', 0)
            ->whereHas('sender', function ($query) {
                // Only count messages from active users with valid roles
                $query->where('status', 1)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('type', [1, 2, 3]);
                    });
            })
            ->count();

        // Count unread team chat messages where user is a member and not removed
        $teamChatCount = \App\Models\ChatMember::where('user_id', $user->id)
            ->where('is_seen', 0)
            ->whereHas('chat', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('chat.team.members', function ($query) use ($user) {
                // Check user is still a member and not removed
                $query->where('user_id', $user->id)
                    ->where('is_removed', false);
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
                Log::info($user_location_country_name . ' ' . $warehouses_location_country_name . ' ' . $wh->name . 'Distance: ' . $distance);

                if (is_null($minDistance) || $distance < $minDistance) {
                    Log::info($minDistance . ' ' . $distance);
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
            return (int) EstoreCart::where('user_id', auth()->user()->id)->sum('quantity');
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
        // $headerLogo = EcomHomeCms::orderByDesc('id')->value('header_logo');
        $headerLogo = self::getVisitorCmsContent('EcomHomeCms', true, false, 'id', 'desc', null)->header_logo ?? null;
        if ($headerLogo) {
            return Storage::url($headerLogo);
        }

        // Final fallback to static asset
        return asset($defaultAsset);
    }

    public static function getOriginalImage($compressedPath)
    {
        $image = GlobalImage::where('compressed_path', $compressedPath)->first();
        return $image ? $image->original_path : null;
    }

    /**
     * Public URL for chat/team media. Prefers the original file when a GlobalImage
     * mapping exists — older compressions used Intervention v3 resize(2000,2000)
     * which squashed portraits into squares.
     */
    public static function chatMediaUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $relative = $path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $storageBase = rtrim(Storage::disk('public')->url(''), '/').'/';
            $appStorage = rtrim(config('app.url'), '/').'/storage/';
            if (str_starts_with($path, $storageBase)) {
                $relative = substr($path, strlen($storageBase));
            } elseif (str_starts_with($path, $appStorage)) {
                $relative = substr($path, strlen($appStorage));
            } else {
                return $path;
            }
        }

        $relative = ltrim($relative, '/');
        if (str_starts_with($relative, 'storage/')) {
            $relative = substr($relative, strlen('storage/'));
        }

        $disk = Storage::disk('public');
        $original = self::getOriginalImage($relative);
        if ($original && $disk->exists($original)) {
            return self::publicStorageUrl($original);
        }

        return self::publicStorageUrl($relative) ?? (str_starts_with($path, 'http') ? $path : Storage::url($relative));
    }

    /**
     * Resolve a storage-relative path to an absolute public URL.
     * Falls back to the original (uncompressed) file when the compressed asset is missing.
     */
    public static function publicStorageUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relative = ltrim($path, '/');
        if (str_starts_with($relative, 'storage/')) {
            $relative = substr($relative, strlen('storage/'));
        }

        $disk = Storage::disk('public');
        $resolved = $relative;

        if (! $disk->exists($resolved)) {
            $original = self::getOriginalImage($relative);
            if ($original && $disk->exists($original)) {
                $resolved = $original;
            } else {
                return null;
            }
        }

        $url = $disk->url($resolved);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($url, '/');
    }

    /**
     * Decode e-store home slider JSON (handles array, JSON string, or double-encoded JSON).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function decodeEcomSliderData(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_is_list($value) ? $value : array_values($value);
        }

        if (! is_string($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            if (array_is_list($decoded)) {
                return $decoded;
            }
            if (isset($decoded['title']) || isset($decoded['image'])) {
                return [$decoded];
            }

            return array_values($decoded);
        }

        // Double-encoded JSON string stored in DB (json string inside json string).
        if (is_string($decoded)) {
            $inner = json_decode($decoded, true);
            if (is_array($inner)) {
                return array_is_list($inner) ? $inner : array_values($inner);
            }
        }

        return [];
    }

    /**
     * @param  array<int, array<string, mixed>>|string|null  $slides
     * @return array<int, array<string, mixed>>
     */
    public static function transformEcomSliderData($slides): array
    {
        $slides = self::decodeEcomSliderData($slides);
        if ($slides === []) {
            return [];
        }

        return array_values(array_map(function ($slide) {
            if (! is_array($slide)) {
                return $slide;
            }
            if (! empty($slide['image'])) {
                $slide['image'] = self::publicStorageUrl($slide['image']);
            }

            return $slide;
        }, $slides));
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    public static function transformEcomHomeContent(array $content): array
    {
        $imageKeys = [
            'header_logo',
            'banner_image',
            'banner_image_small',
            'new_arrival_image',
            'about_section_image',
            'shop_now_image',
        ];

        foreach ($imageKeys as $key) {
            if (! empty($content[$key])) {
                $content[$key] = self::publicStorageUrl($content[$key]);
            }
        }

        if (! empty($content['slider_data'])) {
            $content['slider_data'] = self::transformEcomSliderData($content['slider_data']);
        }

        if (! empty($content['slider_data_second'])) {
            $content['slider_data_second'] = self::transformEcomSliderData($content['slider_data_second']);
        }

        return $content;
    }

    public static function getEstoreProductStartingPrice($productId)
    {
        $warehouseProducts = \App\Models\WarehouseProduct::where('product_id', $productId)->get();
        if ($warehouseProducts->isEmpty()) {
            return null;
        }
        $startingPrice = $warehouseProducts->min('price');
        return $startingPrice;
    }

    /**
     * Resolve the current request to a Country record based on the domain column in DB.
     * Uses a simple static cache to avoid repeated calls within the same request.
     * The Country model itself also caches the DB query, so this is very fast.
     */
    private static $resolvedCountry = null;
    private static $domainResolved = false;

    public static function getCountryByDomain()
    {
        if (self::$domainResolved) {
            return self::$resolvedCountry;
        }
        self::$domainResolved = true;
        self::$resolvedCountry = \App\Models\Country::findByCurrentRequest();
        return self::$resolvedCountry;
    }

    /**
     * Check if the current request is being served by a country-specific instance (not GLOBAL).
     * Checks domain in DB first, falls back to env LION_ROARING_USA.
     */
    public static function isUsaInstance()
    {
        $country = self::getCountryByDomain();
        if ($country && !$country->is_global && strtoupper($country->code) === 'US') {
            return true;
        }

        // Fallback to env-based check for backward compatibility (host + optional path)
        $usaUrl = env('LION_ROARING_USA');
        if (!$usaUrl) {
            return false;
        }

        return \App\Models\Country::requestMatchesDomainUrl($usaUrl);
    }

    /**
     * Return the USA instance URL (from cached collection, no extra DB query).
     */
    public static function getUsaInstanceUrl()
    {
        $domain = \App\Models\Country::getDomainByCode('US');
        if ($domain) {
            return $domain;
        }
        return env('LION_ROARING_USA', '');
    }

    /**
     * Return the MAIN_URL (from cached collection, no extra DB query).
     */
    public static function getMainUrl()
    {
        $domain = \App\Models\Country::getGlobalDomain();
        if ($domain) {
            return $domain;
        }
        return env('MAIN_URL', env('APP_URL', ''));
    }

    /**
     * Check if the current request is being served by the GLOBAL (main) instance.
     */
    public static function isGlobalInstance()
    {
        $country = self::getCountryByDomain();
        return $country && $country->is_global;
    }

    /**
     * Check if the current request is being served by the MAIN instance (not a country-specific domain).
     */
    public static function isMainInstance()
    {
        return !self::isUsaInstance();
    }

    /**
     * Default regional host (lionroaring.us / :8001). Path-based countries live here.
     */
    public static function getDefaultRegionalUrl(): string
    {
        $domain = \App\Models\Country::getDomainByCode('US');
        if ($domain) {
            return rtrim($domain, '/');
        }

        return rtrim((string) env('LION_ROARING_USA', ''), '/');
    }

    public static function isDefaultRegionalInstance(): bool
    {
        return self::isUsaInstance();
    }

    /**
     * Get the redirect URL for a given country code.
     * GL → org (global). Dedicated domain → that domain. Otherwise → us/{code}.
     */
    public static function getCountryRedirectUrl(string $countryCode): string
    {
        $countryCode = strtoupper($countryCode);

        if ($countryCode === 'GL') {
            return self::getMainUrl();
        }

        $domain = \App\Models\Country::getDomainByCode($countryCode);
        if ($domain) {
            return $domain;
        }

        return self::getDefaultRegionalUrl() . '/' . strtolower($countryCode);
    }

    /** @var array<string>|null */
    private static $regionalCountryCodes = null;

    /**
     * First URL path segment when it is an active regional country code (e.g. "in").
     */
    public static function extractCountryCodeFromPath(?string $path = null): ?string
    {
        $path = trim($path ?? request()->path(), '/');
        if ($path === '') {
            return null;
        }

        $segment = strtolower(explode('/', $path)[0]);

        if (self::$regionalCountryCodes === null) {
            self::$regionalCountryCodes = \App\Models\Country::query()
                ->where('is_global', false)
                ->where('status', true)
                ->pluck('code')
                ->map(fn ($code) => strtolower((string) $code))
                ->all();
        }

        if (!in_array($segment, self::$regionalCountryCodes, true)) {
            return null;
        }

        return strtoupper($segment);
    }

    /**
     * Compare redirect target to the current request (scheme-insensitive on same host/path).
     * Uses full request URI path so subdirectory installs (…/lion-roaring-us) compare correctly.
     */
    public static function isRedirectEquivalentToCurrentRequest(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        $currentPath = \App\Models\Country::normalizeUrlPath(
            parse_url(request()->getRequestUri(), PHP_URL_PATH)
        );
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return rtrim(request()->fullUrl(), '/') === rtrim($url, '/');
        }

        if (strtolower($parsed['host']) !== strtolower(request()->getHost())) {
            return false;
        }

        $targetPort = $parsed['port'] ?? null;
        if ($targetPort !== null && (string) $targetPort !== (string) request()->getPort()) {
            return false;
        }

        $targetPath = \App\Models\Country::normalizeUrlPath($parsed['path'] ?? '');

        return $targetPath === $currentPath;
    }

    public static function redirectUrlSharesHostWithRequest(string $url): bool
    {
        $parsed = parse_url($url);

        return isset($parsed['host'])
            && strtolower($parsed['host']) === strtolower(request()->getHost());
    }

    /**
     * True when the request is already on the given country domain URL (host + path prefix).
     */
    public static function isRequestOnCountryDomainUrl(string $domainUrl): bool
    {
        return \App\Models\Country::requestMatchesDomainUrl($domainUrl);
    }

    /**
     * Return redirect URL only when it would change host or path (prevents loops).
     */
    public static function safeExternalRedirectUrl(?string $url): ?string
    {
        if (!$url || self::isRedirectEquivalentToCurrentRequest($url)) {
            return null;
        }

        return $url;
    }

    /**
     * Append ?cc= for cross-domain session handoff (org ↔ us cookies do not transfer).
     */
    public static function appendCountryCodeQueryParam(string $url, string $countryCode): string
    {
        $countryCode = strtolower(trim($countryCode));
        if ($countryCode === '') {
            return $url;
        }

        if (preg_match('/[?&]cc=/i', $url)) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . 'cc=' . $countryCode;
    }

    /**
     * Apply ?cc=GL|in|us from query string, persist session, return clean URL to redirect to.
     */
    public static function consumeVisitorCountryQueryParam(?\Illuminate\Http\Request $request = null): ?string
    {
        $request = $request ?? request();
        $cc = strtoupper(trim((string) $request->query('cc', '')));
        if ($cc === '') {
            return null;
        }

        if ($cc === 'GL') {
            self::setVisitorCountrySession('GL');
        } else {
            $country = \App\Models\Country::query()
                ->where('code', $cc)
                ->where('is_global', false)
                ->where('status', true)
                ->first();
            if (!$country) {
                return null;
            }
            self::setVisitorCountrySession($cc);
        }

        self::$resolvedEffectiveCountry = null;
        self::$effectiveCountryResolved = false;

        return $request->url();
    }

    /**
     * Safe cross-host redirect with ?cc= so the target domain receives the country session.
     */
    public static function countryRedirectWithSessionHandoff(string $url, string $countryCode): ?string
    {
        return self::safeExternalRedirectUrl(
            self::appendCountryCodeQueryParam($url, $countryCode)
        );
    }

    private static $resolvedEffectiveCountry = null;
    private static $effectiveCountryResolved = false;

    /**
     * Effective country for routing and access checks.
     * - org (global) root → GL only; org never hosts regional paths.
     * - us (default regional) root → US; us/{code} → that country.
     * - Dedicated domain root → that country.
     */
    public static function resolveEffectiveCountryFromRequest(): ?\App\Models\Country
    {
        if (self::$effectiveCountryResolved) {
            return self::$resolvedEffectiveCountry;
        }
        self::$effectiveCountryResolved = true;

        $domainCountry = \App\Models\Country::findByCurrentRequest();
        $path = trim(request()->path(), '/');
        $pathCode = self::extractCountryCodeFromPath($path);

        if ($domainCountry && $domainCountry->is_global) {
            if ($pathCode) {
                self::$resolvedEffectiveCountry = $domainCountry;

                return self::$resolvedEffectiveCountry;
            }

            if ($path === '') {
                self::$resolvedEffectiveCountry = $domainCountry;

                return self::$resolvedEffectiveCountry;
            }

            // Ancillary paths on org (login-check, user/*, etc.) are global context.
            self::$resolvedEffectiveCountry = $domainCountry;

            return self::$resolvedEffectiveCountry;
        }

        if ($domainCountry && !$domainCountry->is_global) {
            if ($pathCode && strtoupper($pathCode) !== strtoupper($domainCountry->code)) {
                $country = \App\Models\Country::query()
                    ->whereRaw('LOWER(code) = ?', [strtolower($pathCode)])
                    ->where('is_global', false)
                    ->first();

                if ($country) {
                    self::$resolvedEffectiveCountry = $country;

                    return self::$resolvedEffectiveCountry;
                }
            }

            if (!$pathCode && $path !== '') {
                $ip = request()->ip();
                $sessionCode = strtoupper((string) session('visitor_country_code_' . $ip, ''));
                if ($sessionCode && $sessionCode !== 'GL' && strtoupper($sessionCode) !== strtoupper($domainCountry->code)) {
                    $sessionCountry = \App\Models\Country::query()
                        ->where('code', $sessionCode)
                        ->where('is_global', false)
                        ->first();

                    if ($sessionCountry) {
                        self::$resolvedEffectiveCountry = $sessionCountry;

                        return self::$resolvedEffectiveCountry;
                    }
                }
            }

            self::$resolvedEffectiveCountry = $domainCountry;

            return self::$resolvedEffectiveCountry;
        }

        self::$resolvedEffectiveCountry = $domainCountry
            ?? \App\Models\Country::query()->where('is_global', true)->first();

        return self::$resolvedEffectiveCountry;
    }

    public static function isEffectiveGlobalContext(): bool
    {
        $effective = self::resolveEffectiveCountryFromRequest();

        return $effective && $effective->is_global;
    }

    /**
     * Country shown in UI badges (header, profile). Logged-in users use their type/country;
     * guests use the effective request country.
     */
    public static function getDisplayCountry(): ?\App\Models\Country
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->user_type === 'Global') {
                return \App\Models\Country::query()->where('is_global', true)->first();
            }

            if ($user->user_type === 'Regional' && $user->country) {
                return \App\Models\Country::find($user->country);
            }

            if ($user->user_type === 'G_R') {
                $effective = self::resolveEffectiveCountryFromRequest();
                if ($effective && !$effective->is_global) {
                    return $effective;
                }

                return \App\Models\Country::query()->where('is_global', true)->first();
            }
        }

        return self::resolveEffectiveCountryFromRequest();
    }

    /**
     * On org root (/), redirect regional session to us/{code} (or dedicated domain).
     */
    public static function resolveSessionCountryRedirectOnGlobalRoot(): ?string
    {
        if (!self::isGlobalInstance()) {
            return null;
        }

        if (trim(request()->path(), '/') !== '') {
            return null;
        }

        $sessionCode = strtoupper(self::getVisitorCountryCode() ?: '');
        if (!$sessionCode || $sessionCode === 'GL') {
            return null;
        }

        $country = \App\Models\Country::query()
            ->where('code', $sessionCode)
            ->where('is_global', false)
            ->where('status', true)
            ->first();

        if (!$country) {
            return null;
        }

        $url = self::getCountryRedirectUrl($sessionCode);
        if (self::redirectUrlSharesHostWithRequest($url)) {
            // Same-host installs (path-based demo folders): go to the country domain path,
            // do not append /{code} which causes redirect loops.
            $dedicated = \App\Models\Country::getDomainByCode($sessionCode);
            if ($dedicated) {
                $dedicatedPath = \App\Models\Country::normalizeUrlPath(
                    parse_url($dedicated, PHP_URL_PATH)
                );
                if ($dedicatedPath !== '') {
                    return self::countryRedirectWithSessionHandoff($dedicated, $sessionCode);
                }
            }

            $regionalTarget = rtrim(self::getDefaultRegionalUrl(), '/')
                . '/' . strtolower($sessionCode);

            return self::countryRedirectWithSessionHandoff($regionalTarget, $sessionCode);
        }

        return self::countryRedirectWithSessionHandoff($url, $sessionCode);
    }

    /**
     * On default regional root (us/), redirect to path country or org when GL selected.
     */
    public static function resolveSessionCountryRedirectOnRegionalRoot(): ?string
    {
        if (!self::isDefaultRegionalInstance()) {
            return null;
        }

        if (trim(request()->path(), '/') !== '') {
            return null;
        }

        $sessionCode = strtoupper(self::getVisitorCountryCode() ?: '');
        if (!$sessionCode) {
            return null;
        }

        if ($sessionCode === 'GL') {
            return self::countryRedirectWithSessionHandoff(self::getMainUrl(), 'GL');
        }

        $domainCountry = \App\Models\Country::findByCurrentRequest();
        if ($domainCountry && strtoupper($domainCountry->code) === $sessionCode) {
            return null;
        }

        $country = \App\Models\Country::query()
            ->where('code', $sessionCode)
            ->where('is_global', false)
            ->where('status', true)
            ->first();

        if (!$country) {
            return null;
        }

        return self::countryRedirectWithSessionHandoff(
            self::getCountryRedirectUrl($sessionCode),
            $sessionCode
        );
    }

    /**
     * When the URL path starts with a country code, return a redirect to the canonical URL if needed.
     */
    public static function resolveCanonicalRedirectForPathCountry(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);
        $canonical = rtrim(self::getCountryRedirectUrl($countryCode), '/');
        $dedicatedDomain = \App\Models\Country::getDomainByCode($countryCode);

        $path = trim(request()->path(), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $suffixSegments = array_slice($segments, 1);
        $suffix = $suffixSegments ? '/' . implode('/', $suffixSegments) : '';

        if ($dedicatedDomain) {
            $onCorrectDomain = self::isRequestOnCountryDomainUrl($dedicatedDomain);

            if (!$onCorrectDomain) {
                return self::countryRedirectWithSessionHandoff($canonical . $suffix, $countryCode);
            }

            // Already on this country's dedicated domain (host and/or path).
            // If Laravel app path still has /{code} (e.g. /us), strip it back to the domain root.
            if (!empty($segments) && strtoupper($segments[0]) === $countryCode) {
                // Never bounce path-based same-host demos through a fake /{code} regional URL.
                return self::safeExternalRedirectUrl(
                    rtrim($dedicatedDomain, '/') . $suffix
                );
            }

            return null;
        }

        $regionalBase = self::getDefaultRegionalUrl();
        $expectedPrefix = $regionalBase . '/' . strtolower($countryCode);
        $current = rtrim(request()->url(), '/');

        if ($current !== $expectedPrefix && !str_starts_with($current, $expectedPrefix . '/')) {
            if (self::isGlobalInstance() && self::redirectUrlSharesHostWithRequest($expectedPrefix)) {
                // Same host path-based global install: do not invent a /{code} loop target.
                if (self::isRequestOnCountryDomainUrl(self::getMainUrl() ?: '')) {
                    return null;
                }

                return null;
            }

            return self::countryRedirectWithSessionHandoff($expectedPrefix . $suffix, $countryCode);
        }

        return null;
    }

    /**
     * Check if the visitor has already selected a country (stored in session).
     */
    public static function hasCountrySelected()
    {
        $ip = request()->ip();
        $codeSessionKey = 'visitor_country_code_' . $ip;
        return session()->has($codeSessionKey);
    }

    /**
     * Persist visitor country selection in session (code, name, languages).
     */
    public static function setVisitorCountrySession(string $countryCode): void
    {
        $countryCode = strtoupper(trim($countryCode));
        $ip = request()->ip();
        $codeSessionKey = 'visitor_country_code_' . $ip;
        $nameSessionKey = 'visitor_country_name_' . $ip;
        $languageSessionKey = 'visitor_country_languages';

        if ($countryCode === 'GL') {
            $allLanguages = \App\Models\TranslateLanguage::orderBy('name', 'asc')->get();
            $row = \App\Models\Country::query()->where('is_global', true)->first();
            session([
                $codeSessionKey => 'GL',
                $nameSessionKey => $row ? $row->name : 'Global (Main)',
                $languageSessionKey => $allLanguages,
            ]);

            return;
        }

        $countryData = \App\Models\Country::with('languages')->where('code', $countryCode)->first();
        $languages = $countryData ? $countryData->languages : collect();

        $hasEnglish = $languages instanceof \Illuminate\Support\Collection
            ? $languages->contains(fn ($lang) => strtolower($lang->code ?? '') === 'en')
            : false;
        if (!$hasEnglish) {
            $english = \App\Models\TranslateLanguage::whereRaw('LOWER(code) = ?', ['en'])->first();
            if ($english) {
                $languages = $languages instanceof \Illuminate\Support\Collection
                    ? $languages->push($english)
                    : collect([$english]);
            }
        }

        session([
            $codeSessionKey => $countryCode,
            $nameSessionKey => $countryData->name ?? $countryCode,
            $languageSessionKey => $languages,
        ]);
    }

    /**
     * Clear visitor country, partner filters, and login-context session data.
     */
    public static function clearBrowsingSession(): void
    {
        $ip = request()->ip();

        session()->forget([
            'partner_filters',
            'auth_login_host',
            'auth_login_port',
            'auth_login_country_id',
            'visitor_country_code_' . $ip,
            'visitor_country_name_' . $ip,
            'visitor_country_flag_code_' . $ip,
            'visitor_country_languages',
            'user_id',
        ]);

        self::$resolvedEffectiveCountry = null;
        self::$effectiveCountryResolved = false;
    }

    /**
     * After login, align visitor country session with the authenticated user.
     */
    public static function syncVisitorCountryForUser($user = null): void
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return;
        }

        if ($user->user_type === 'Global') {
            self::setVisitorCountrySession('GL');

            return;
        }

        if ($user->user_type === 'G_R') {
            if (self::isGlobalInstance()) {
                self::setVisitorCountrySession('GL');
            } elseif ($user->country) {
                $country = \App\Models\Country::find($user->country);
                if ($country) {
                    self::setVisitorCountrySession($country->code);
                }
            }

            return;
        }

        if ($user->country) {
            $country = \App\Models\Country::find($user->country);
            if ($country) {
                self::setVisitorCountrySession($country->code);
            }
        }
    }

    // get visitor country code by ip using ipinfo.io
    public static function getVisitorCountryCode()
    {
        $ip = request()->ip();
        $codeSessionKey = 'visitor_country_code_' . $ip;
        $nameSessionKey = 'visitor_country_name_' . $ip;

        if (self::isDefaultRegionalInstance()) {
            $pathCode = self::extractCountryCodeFromPath();
            if ($pathCode) {
                if (!session()->has($codeSessionKey) || strtoupper((string) session($codeSessionKey)) !== $pathCode) {
                    self::setVisitorCountrySession($pathCode);
                }

                return $pathCode;
            }

            if (session()->has($codeSessionKey) && session()->has($nameSessionKey)) {
                return session($codeSessionKey);
            }

            self::setVisitorCountrySession('US');

            return 'US';
        }

        // For MAIN_URL: check session first — if a country was explicitly selected, use it
        if (session()->has($codeSessionKey) && session()->has($nameSessionKey)) {
            return session($codeSessionKey);
        }

        // On MAIN_URL with no country selected: return empty string (no auto-detection)
        return '';
    }

    /**
     * Country code for scoped queries (members list, CMS, etc.).
     * Request country_code param → session → effective request country.
     */
    public static function resolveVisitorCountryCode(?\Illuminate\Http\Request $request = null): string
    {
        $request = $request ?? request();

        $param = strtoupper(trim((string) $request->input('country_code', '')));
        if ($param !== '') {
            return $param;
        }

        $sessionCode = strtoupper(trim((string) self::getVisitorCountryCode()));
        if ($sessionCode !== '') {
            return $sessionCode;
        }

        $effective = self::resolveEffectiveCountryFromRequest();
        if ($effective) {
            return $effective->is_global ? 'GL' : strtoupper((string) $effective->code);
        }

        return '';
    }

    // get visitor country name
    public static function getVisitorCountryName()
    {
        $ip = request()->ip();
        $nameSessionKey = 'visitor_country_name_' . $ip;

        // Check session first
        if (session()->has($nameSessionKey)) {
            return session($nameSessionKey);
        }

        // If not in session, call getVisitorCountryCode to populate both code and name
        self::getVisitorCountryCode();

        return session($nameSessionKey, 'United States');
    }

    /**
     * Return the languages to show in the language dropdown.
     * - If on LION_ROARING_USA: US-specific languages
     * - If a country is selected: that country's languages
     * - If on MAIN_URL with no country selected: ALL active languages
     */
    public static function getVisitorCountryLanguages()
    {
        $languageSessionKey = 'visitor_country_languages';

        // If languages are already in session (country selected or USA instance), use those
        if (session()->has($languageSessionKey)) {
            return session($languageSessionKey);
        }

        // Check if we are on GLOBAL domain
        if (self::isGlobalInstance()) {
            $row = \App\Models\Country::with('languages')->where('is_global', true)->first();
            if ($row && $row->languages->isNotEmpty()) {
                return $row->languages;
            }
        }

        // Fallback: all active languages
        $allLanguages = \App\Models\TranslateLanguage::orderBy('name', 'asc')->get();
        return $allLanguages;
    }

    /**
     * Languages available for a country code (stateless; used by mobile API).
     * GL = global country languages or all translate_languages.
     * Regional = country's linked languages, always including English.
     */
    public static function getLanguagesForCountryCode(string $countryCode): \Illuminate\Support\Collection
    {
        $code = strtoupper(trim($countryCode));

        if ($code === '' || $code === 'GL') {
            $row = \App\Models\Country::with('languages')->where('is_global', true)->first();
            if ($row && $row->languages->isNotEmpty()) {
                return $row->languages->sortBy('name')->values();
            }

            return \App\Models\TranslateLanguage::orderBy('name', 'asc')->get();
        }

        $row = \App\Models\Country::with('languages')->whereRaw('UPPER(code) = ?', [$code])->first();
        $languages = $row ? $row->languages : collect();

        $hasEnglish = $languages->contains(fn ($lang) => strtolower($lang->code ?? '') === 'en');
        if (! $hasEnglish) {
            $english = \App\Models\TranslateLanguage::whereRaw('LOWER(code) = ?', ['en'])->first();
            if ($english) {
                $languages = $languages->push($english);
            }
        }

        return $languages->sortBy('name')->values();
    }

    /**
     * Refresh the visitor_country_languages session with fresh data from DB.
     * Call this after adding/updating languages on a country via Country Management
     * so that the chatbot, e-commerce, and all other pages immediately reflect the changes.
     */
    public static function refreshCountryLanguagesSession()
    {
        $ip = request()->ip();
        $codeSessionKey = 'visitor_country_code_' . $ip;
        $languageSessionKey = 'visitor_country_languages';

        $currentCode = session($codeSessionKey);

        if (empty($currentCode)) {
            return;
        }

        if ($currentCode === 'GL') {
            // Global: re-fetch from the global country or fall back to all languages
            $row = \App\Models\Country::with('languages')->where('is_global', true)->first();
            $languages = ($row && $row->languages->isNotEmpty()) ? $row->languages : \App\Models\TranslateLanguage::orderBy('name', 'asc')->get();
        } else {
            // Specific country: re-fetch from DB
            $row = \App\Models\Country::with('languages')->whereRaw('UPPER(code) = ?', [strtoupper($currentCode)])->first();
            $languages = $row ? $row->languages : collect();

            // Ensure English is always included
            $hasEnglish = $languages->contains(fn($lang) => strtolower($lang->code ?? '') === 'en');
            if (!$hasEnglish) {
                $english = \App\Models\TranslateLanguage::whereRaw('LOWER(code) = ?', ['en'])->first();
                if ($english) {
                    $languages = $languages->push($english);
                }
            }
        }

        session([$languageSessionKey => $languages]);
    }

    // get visitor cms content by model name, single row or multiple, if multiple then pagination true/false, sort by, optional search query
    public static function getVisitorCmsContent($modelClass, $singleRow = true, $paginate = false, $sortBy = 'id', $sortType = 'desc', $searchQuery = null)
    {
        $countryCode = self::getVisitorCountryCode();

        // If no country is selected or GLOBAL is selected, fall back to US content
        if (empty($countryCode)) {
            $countryCode = 'US';
        }

        $modelClass = "\App\Models\\" . $modelClass;


        if (!class_exists($modelClass)) {
            return $singleRow ? null : collect();
        }

        // return $modelClass;

        $applySearch = function ($query) use ($searchQuery) {
            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'like', '%' . $searchQuery . '%')
                        ->orWhere('description', 'like', '%' . $searchQuery . '%');
                });
            }
        };

        $buildQueryFor = function ($code) use ($modelClass, $applySearch, $sortBy, $sortType) {
            $q = $modelClass::where('country_code', $code);
            $applySearch($q);
            $q->orderBy($sortBy, $sortType);
            return $q;
        };

        // Try for visitor country first
        $query = $buildQueryFor($countryCode);


        if ($singleRow) {
            $result = $query->first();

            // Fallback to US if nothing found and visitor country isn't US
            if (is_null($result) && $countryCode !== 'US') {
                $result = $buildQueryFor('US')->first();
            }

            return $result;
        }

        if ($paginate) {
            $results = $query->paginate(10);

            if ($results->isEmpty() && $countryCode !== 'US') {
                $results = $buildQueryFor('US')->paginate(10);
            }

            return $results;
        }

        $results = $query->get();

        if ($results->isEmpty() && $countryCode !== 'US') {
            $results = $buildQueryFor('US')->get();
        }

        return $results;
    }

    /**
     * Return dynamic menu item name by key, otherwise return default
     *
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public static function getMenuName(string $key, ?string $default = null): string
    {
        try {
            $menu = MenuItem::where('key', $key)->first();
            if ($menu && !empty($menu->name)) {
                return $menu->name;
            }
            if ($menu && !empty($menu->default_name)) {
                return $menu->default_name;
            }
        } catch (\Exception $e) {
            // ignore and fall back to default
        }

        return $default ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Whether the user may access content for a visitor country code (mobile app / API).
     */
    public static function userCanAccessCountryContext($user, string $countryCode): bool
    {
        if (!$user || $user->hasNewRole('SUPER ADMIN')) {
            return true;
        }

        $code = strtoupper(trim($countryCode));
        if ($code === '') {
            return true;
        }

        $effectiveCountry = \App\Models\Country::where('code', $code)->first();
        if (!$effectiveCountry) {
            return false;
        }

        $isGlobalContext = (bool) $effectiveCountry->is_global;
        $userCountry = $user->country ? \App\Models\Country::find($user->country) : null;

        switch ($user->user_type) {
            case 'Global':
                return $isGlobalContext;

            case 'Regional':
                return !$isGlobalContext
                    && $userCountry
                    && (int) $effectiveCountry->id === (int) $userCountry->id;

            case 'G_R':
                if ($isGlobalContext) {
                    return true;
                }

                return $userCountry
                    && (int) $effectiveCountry->id === (int) $userCountry->id;

            default:
                return true;
        }
    }

    /**
     * Whether the user's type is allowed on the current domain instance.
     */
    public static function userCanAccessCurrentInstance($user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return true;
        }

        if ($user->hasNewRole('SUPER ADMIN')) {
            return true;
        }

        $effectiveCountry = self::resolveEffectiveCountryFromRequest();
        if (!$effectiveCountry) {
            return true;
        }

        $isGlobalContext = (bool) $effectiveCountry->is_global;
        $userCountry = $user->country ? \App\Models\Country::find($user->country) : null;

        switch ($user->user_type) {
            case 'Global':
                return self::isGlobalInstance() && $isGlobalContext;

            case 'Regional':
                if (self::isGlobalInstance()) {
                    return false;
                }

                return $userCountry
                    && (int) $effectiveCountry->id === (int) $userCountry->id;

            case 'G_R':
                if (self::isGlobalInstance()) {
                    return $isGlobalContext;
                }

                return $userCountry
                    && (int) $effectiveCountry->id === (int) $userCountry->id;

            default:
                return true;
        }
    }

    /**
     * URL for the instance this user should use when on the wrong domain.
     */
    public static function resolveUserInstanceRedirectUrl($user = null): ?string
    {
        $user = $user ?? auth()->user();
        if (!$user || self::userCanAccessCurrentInstance($user)) {
            return null;
        }

        $userCountry = $user->country ? \App\Models\Country::find($user->country) : null;

        if ($user->user_type === 'Regional' && $userCountry) {
            return self::getCountryRedirectUrl($userCountry->code);
        }

        if ($user->user_type === 'Global') {
            return self::getMainUrl();
        }

        if ($user->user_type === 'G_R') {
            if ($userCountry) {
                return self::getCountryRedirectUrl($userCountry->code);
            }

            return self::getMainUrl();
        }

        return self::getMainUrl();
    }

    public static function userInstanceAccessMessage($user = null): string
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return 'You do not have access to this site instance.';
        }

        $userCountry = $user->country ? \App\Models\Country::find($user->country) : null;

        switch ($user->user_type) {
            case 'Regional':
                return 'You are a Regional user. Please sign in on your assigned country site'
                    . ($userCountry ? ' (' . $userCountry->name . ').' : '.');

            case 'Global':
                return 'You are a Global user. Please sign in on the Global site.';

            case 'G_R':
                return 'Please use the Global site or your assigned regional site'
                    . ($userCountry ? ' (' . $userCountry->name . ').' : '.');

            default:
                return 'You do not have access to this site instance.';
        }
    }

    /**
     * Remember which domain the user signed in on.
     */
    public static function recordLoginContext(): void
    {
        $effectiveCountry = self::resolveEffectiveCountryFromRequest();

        session([
            'auth_login_host' => request()->getHost(),
            'auth_login_port' => request()->getPort(),
            'auth_login_country_id' => $effectiveCountry ? (int) $effectiveCountry->id : null,
        ]);
    }

    /**
     * True when the current request is on the same domain the user signed in on.
     */
    public static function loginContextMatchesCurrentRequest(): bool
    {
        if (!session()->has('auth_login_host')) {
            return true;
        }

        if (session('auth_login_host') !== request()->getHost()) {
            return false;
        }

        if ((string) session('auth_login_port') !== (string) request()->getPort()) {
            return false;
        }

        $loginCountryId = session('auth_login_country_id');
        $effective = self::resolveEffectiveCountryFromRequest();
        $effectiveId = $effective ? (int) $effective->id : null;

        return (int) $loginCountryId === (int) $effectiveId;
    }

    /**
     * User type + sign-in domain must both match the current site.
     */
    public static function userHasValidInstanceSession($user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return true;
        }

        return self::userCanAccessCurrentInstance($user)
            && self::loginContextMatchesCurrentRequest();
    }

    public static function userInstanceLogoutMessage($user = null): string
    {
        if (!self::loginContextMatchesCurrentRequest()) {
            return 'You have been signed out because this session was started on a different site. Please sign in again here.';
        }

        return self::userInstanceAccessMessage($user) . ' You have been signed out.';
    }

    public static function getTimezoneFromIp(?string $ip = null): ?string
    {
        $ip = $ip ?? request()->ip();
        if (empty($ip) || in_array($ip, ['127.0.0.1', '::1'], true)) {
            return config('app.timezone');
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return config('app.timezone');
        }

        return Cache::remember("timezone_for_ip:{$ip}", now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'timezone',
                ]);

                if (! $response->successful()) {
                    return null;
                }

                $timezone = $response->json('timezone');

                return is_string($timezone) && $timezone !== '' ? $timezone : null;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    public static function getTimeBasedGreeting(?string $ip = null): string
    {
        $timezone = self::getTimezoneFromIp($ip) ?? config('app.timezone');
        $hour = (int) now()->timezone($timezone)->format('H');

        if ($hour < 12) {
            return 'Perfect morning';
        }
        if ($hour < 17) {
            return 'Perfect afternoon';
        }

        return 'Perfect evening';
    }
}
