<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\Country;
use App\Models\EcomCmsPage;
use App\Models\EcomFooterCms;
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

class Helper
{
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
        $pages = EcomCmsPage::get();
        return $pages;
    }

    public static function getFooterCms()
    {
        $cms = EcomFooterCms::orderBy('id', 'desc')->first();
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
}
