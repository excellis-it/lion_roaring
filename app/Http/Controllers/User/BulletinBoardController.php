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
            $bulletins = $this->fetchBulletinsForUser($user);

            return view('user.bulletin-board.list')->with('bulletins', $bulletins);
        }

        abort(403, 'You do not have permission to access this page.');
    }

    public function load(Request $request)
    {
        $user = auth()->user();
        $bulletins = $this->fetchBulletinsForUser($user);

        return response()->json([
            'view' => view('user.bulletin-board.show-bulletin')->with('bulletins', $bulletins)->render(),
        ]);
    }

    /**
     * Translate bulletin title/description after the board has already rendered (async).
     */
    public function translateContent(Request $request)
    {
        $user = auth()->user();
        if (!$user->can('Manage Bulletin')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $targetLang = ContentTranslationService::resolveTargetLanguage(
            $_COOKIE['googtrans'] ?? null,
            $_COOKIE['content_lang'] ?? null
        );

        if ($targetLang === null && $request->filled('target')) {
            $normalized = ContentTranslationService::normalizeLangCode((string) $request->input('target'));
            $targetLang = $normalized !== '' ? $normalized : null;
        }

        if ($targetLang === null) {
            return response()->json(['items' => []]);
        }

        $bulletins = $this->fetchBulletinsForUser($user);
        if ($bulletins->isEmpty()) {
            return response()->json(['items' => []]);
        }

        $flat = [];
        foreach ($bulletins as $bulletin) {
            $flat[$bulletin->id . ':title'] = (string) ($bulletin->title ?? '');
            $flat[$bulletin->id . ':description'] = (string) ($bulletin->description ?? '');
        }

        $translated = ContentTranslationService::translateMany($flat, $targetLang);

        $items = [];
        foreach ($bulletins as $bulletin) {
            $title = $translated[$bulletin->id . ':title'] ?? (string) ($bulletin->title ?? '');
            $description = $translated[$bulletin->id . ':description'] ?? (string) ($bulletin->description ?? '');
            $items[] = [
                'id' => $bulletin->id,
                'title' => $title,
                'description_html' => $this->formatBulletinDescription($description),
            ];
        }

        return response()->json(['items' => $items, 'target' => $targetLang]);
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

    private function formatBulletinDescription(string $description): string
    {
        if ($description === '') {
            return '';
        }

        return preg_replace_callback(
            '/(https?:\/\/[^\s]+)/',
            function ($m) {
                $url = $m[1];
                $short = strlen($url) > 40 ? substr($url, 0, 40) . '...' : $url;

                return '<a href="' .
                    $url .
                    '" target="_blank" style="color:#0d6efd; text-decoration:underline;">' .
                    $short .
                    '</a>';
            },
            nl2br(e($description))
        );
    }
}
