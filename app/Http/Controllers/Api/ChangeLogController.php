<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use Illuminate\Http\Request;

class ChangeLogController extends Controller
{
    public function index(Request $request)
    {
        $platform = in_array($request->platform, ['web', 'mobile'], true)
            ? $request->platform
            : 'web';

        $paginator = ChangeLog::forPlatform($platform)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        $paginator->getCollection()->transform(fn (ChangeLog $log) => [
            'id' => $log->id,
            'version' => $log->version,
            'title' => $log->title,
            'type' => $log->type,
            'platform' => $log->platform,
            'description' => $log->description,
            'published_at' => optional($log->published_at)?->toIso8601String(),
        ]);

        $settings = Helper::getSettings();
        $currentVersion = null;
        if ($settings) {
            $currentVersion = $platform === 'mobile'
                ? ($settings->MOBILE_APP_VERSION ?? null)
                : ($settings->WEB_APP_VERSION ?? null);
        }

        return response()->json([
            'status' => true,
            'message' => 'OK',
            'platform' => $platform,
            'current_version' => $currentVersion,
            'data' => $paginator,
        ]);
    }
}
