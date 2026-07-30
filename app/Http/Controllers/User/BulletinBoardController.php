<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\Country;
use App\Services\ContentTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BulletinBoardController extends Controller
{
    public function list()
    {
        $user = auth()->user();
        if ($user->can('Manage Bulletin')) {
            $bulletins = $this->applyTranslations($this->fetchBulletinsForUser($user));

            return view('user.bulletin-board.list')->with('bulletins', $bulletins);
        }

        abort(403, 'You do not have permission to access this page.');
    }

    public function load(Request $request)
    {
        $user = auth()->user();
        $bulletins = $this->applyTranslations($this->fetchBulletinsForUser($user));

        return response()->json([
            'view' => view('user.bulletin-board.show-bulletin')->with('bulletins', $bulletins)->render(),
        ]);
    }

    /**
     * Server-translate bulletin UGC for the visitor's selected content language.
     * Original / empty cookie leaves posts in the author's language.
     */
    private function applyTranslations(Collection $bulletins): Collection
    {
        $contentLang = isset($_COOKIE['content_lang']) ? trim((string) $_COOKIE['content_lang']) : null;
        if ($contentLang === null || $contentLang === '' || $contentLang === '__original__') {
            return $bulletins;
        }

        $targetLang = ContentTranslationService::resolveTargetLanguage(
            $_COOKIE['googtrans'] ?? null,
            $contentLang
        );

        if ($targetLang === null || $targetLang === '') {
            return $bulletins;
        }

        foreach ($bulletins as $bulletin) {
            ContentTranslationService::translateBulletinFields($bulletin, $targetLang);
        }

        return $bulletins;
    }

    private function fetchBulletinsForUser($user): Collection
    {
        $user_type = $user->user_type;
        $user_country = $user->country;
        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

        if (!$user->hasNewRole('SUPER ADMIN')) {
            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                return Bulletin::orderBy('id', 'desc')->whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })->whereHas('user', function ($query) {
                    $query->whereIn('user_type', ['Global', 'G_R'])->where('status', 1);
                })->get();
            }

            $bulletins = Bulletin::orderBy('id', 'desc')->where('country_id', $user_country)->whereHas('user', function ($query) {
                $query->whereIn('user_type', ['Regional', 'G_R'])->where('status', 1);
            });
            if ($user->is_ecclesia_admin == 1) {
                $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                    ? $user->manage_ecclesia
                    : explode(',', $user->manage_ecclesia ?? '');
                $bulletins->where(function ($q) use ($manage_ecclesia_ids, $user) {
                    $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                        $uq->where(function ($sub) use ($manage_ecclesia_ids) {
                            $sub->whereIn('ecclesia_id', $manage_ecclesia_ids)->whereNotNull('ecclesia_id');
                            foreach ($manage_ecclesia_ids as $id) {
                                $sub->orWhereRaw('FIND_IN_SET(?, manage_ecclesia)', [trim($id)]);
                            }
                        });
                    })->orWhere('user_id', $user->id);
                });
            }

            return $bulletins->get();
        }

        return Bulletin::orderBy('id', 'desc')->get();
    }
}
