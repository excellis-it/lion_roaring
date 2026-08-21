<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ChangeLogController extends Controller
{
    private function canManage(): bool
    {
        $user = auth()->user();

        return $user->hasNewRole('SUPER ADMIN') || $user->can('Manage Change Logs');
    }

    private function canView(): bool
    {
        return $this->canManage() || auth()->user()->can('View Change Logs');
    }

    private function canCreate(): bool
    {
        return $this->canManage() || auth()->user()->can('Create Change Logs');
    }

    private function canEdit(): bool
    {
        return $this->canManage() || auth()->user()->can('Edit Change Logs');
    }

    private function canDelete(): bool
    {
        return $this->canManage() || auth()->user()->can('Delete Change Logs');
    }

    private function userTimezone(): string
    {
        return auth()->user()->time_zone ?? config('app.timezone');
    }

    /**
     * Parse a datetime-local value as the user's wall time and store in app timezone.
     * Blank => publish immediately. Future values are rejected (no scheduled publish).
     */
    private function resolvePublishedAt(?string $value): Carbon
    {
        if ($value === null || trim($value) === '') {
            return now();
        }

        $parsed = Carbon::parse($value, $this->userTimezone())
            ->timezone(config('app.timezone'));

        if ($parsed->gt(now())) {
            throw ValidationException::withMessages([
                'published_at' => 'Publish date cannot be in the future. Entries publish immediately on save.',
            ]);
        }

        return $parsed;
    }

    private function sanitizeDescription(string $html): string
    {
        static $purifier = null;

        if ($purifier === null) {
            $cachePath = storage_path('app/htmlpurifier');
            if (!is_dir($cachePath)) {
                @mkdir($cachePath, 0755, true);
            }

            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'b,strong,i,em,u,br,p,ul,ol,li');
            $config->set('Cache.SerializerPath', $cachePath);
            $purifier = new \HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }

    public function index(Request $request)
    {
        if (!$this->canView()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $platform = in_array($request->platform, ['web', 'mobile'], true)
            ? $request->platform
            : 'web';

        $query = ChangeLog::forPlatform($platform);

        if ($this->canManage()) {
            $changeLogs = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
                ->paginate(15)
                ->withQueryString();
        } else {
            $changeLogs = $query->published()
                ->orderBy('published_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

        $settings = Helper::getSettings();
        $webVersion = $settings->WEB_APP_VERSION ?? null;
        $mobileVersion = $settings->MOBILE_APP_VERSION ?? null;

        $canCreate = $this->canCreate();
        $canEdit = $this->canEdit();
        $canDelete = $this->canDelete();

        return view('user.change-logs.index', compact(
            'changeLogs',
            'canCreate',
            'canEdit',
            'canDelete',
            'platform',
            'webVersion',
            'mobileVersion'
        ));
    }

    public function updateVersions(Request $request)
    {
        if (!$this->canEdit()) {
            abort(403, 'You do not have permission to edit version numbers.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'WEB_APP_VERSION' => 'nullable|string|max:50',
            'MOBILE_APP_VERSION' => 'nullable|string|max:50',
        ]);

        $settings = SiteSetting::first();
        if (!$settings) {
            return redirect()->route('change-logs.index')
                ->with('error', 'Site settings not found.');
        }

        $settings->WEB_APP_VERSION = $validated['WEB_APP_VERSION'] ?? null;
        $settings->MOBILE_APP_VERSION = $validated['MOBILE_APP_VERSION'] ?? null;
        $settings->save();

        Helper::forgetSettingsCache();

        $platform = in_array($request->platform, ['web', 'mobile'], true)
            ? $request->platform
            : 'web';

        return redirect()->route('change-logs.index', ['platform' => $platform])
            ->with('message', 'App versions updated successfully.');
    }

    public function create(Request $request)
    {
        if (!$this->canCreate()) {
            abort(403, 'You do not have permission to create change logs.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $platform = in_array($request->platform, ['web', 'mobile'], true)
            ? $request->platform
            : 'web';

        $defaultPublishedAt = now()
            ->timezone($this->userTimezone())
            ->format('Y-m-d\TH:i');

        return view('user.change-logs.create', compact('platform', 'defaultPublishedAt'));
    }

    public function store(Request $request)
    {
        if (!$this->canCreate()) {
            abort(403, 'You do not have permission to create change logs.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'type' => 'required|in:feature,improvement,bugfix,security',
            'platform' => 'required|in:web,mobile',
            'description' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $description = $this->sanitizeDescription($validated['description']);
        if (trim(strip_tags($description)) === '') {
            return back()->withErrors(['description' => 'The description field is required.'])->withInput();
        }

        ChangeLog::create([
            'created_by' => auth()->id(),
            'version' => $validated['version'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'platform' => $validated['platform'],
            'description' => $description,
            'published_at' => $this->resolvePublishedAt($validated['published_at'] ?? null),
        ]);

        return redirect()->route('change-logs.index', ['platform' => $validated['platform']])
            ->with('message', 'Change log entry created successfully.');
    }

    public function edit(ChangeLog $changeLog)
    {
        if (!$this->canEdit()) {
            abort(403, 'You do not have permission to edit change logs.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $publishedAtLocal = $changeLog->published_at
            ? $changeLog->published_at->timezone($this->userTimezone())->format('Y-m-d\TH:i')
            : null;

        return view('user.change-logs.edit', compact('changeLog', 'publishedAtLocal'));
    }

    public function update(Request $request, ChangeLog $changeLog)
    {
        if (!$this->canEdit()) {
            abort(403, 'You do not have permission to edit change logs.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'type' => 'required|in:feature,improvement,bugfix,security',
            'platform' => 'required|in:web,mobile',
            'description' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $validated['description'] = $this->sanitizeDescription($validated['description']);
        if (trim(strip_tags($validated['description'])) === '') {
            return back()->withErrors(['description' => 'The description field is required.'])->withInput();
        }

        $validated['published_at'] = $this->resolvePublishedAt($validated['published_at'] ?? null);

        $changeLog->update(Arr::except($validated, ['created_by']));

        return redirect()->route('change-logs.index', ['platform' => $validated['platform']])
            ->with('message', 'Change log entry updated successfully.');
    }

    public function destroy(ChangeLog $changeLog)
    {
        if (!$this->canDelete()) {
            abort(403, 'You do not have permission to delete change logs.');
        }

        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $platform = $changeLog->platform;
        $changeLog->delete();

        return redirect()->route('change-logs.index', ['platform' => $platform])
            ->with('message', 'Change log entry deleted.');
    }
}
