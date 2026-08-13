<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserActivityExportService
{
    public const CHUNK_SIZE = 5000;

    private const CACHE_TTL_SECONDS = 7200;

    public const HEADINGS = [
        'Name',
        'Email',
        'Username',
        'Ecclesia Name',
        'IP',
        'Country Code',
        'Country Name',
        'Device Type',
        'Browser',
        'URL',
        'Activity Type',
        'Activity Date',
    ];

    public function filteredQuery(array $filters)
    {
        $query = UserActivity::query()
            ->leftJoin('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('countries', 'users.country', '=', 'countries.id');

        if (! empty($filters['user_name'])) {
            $query->where('user_activities.user_name', 'like', '%' . $filters['user_name'] . '%');
        }
        if (! empty($filters['email'])) {
            $query->where('user_activities.email', 'like', '%' . $filters['email'] . '%');
        }
        if (! empty($filters['user_roles'])) {
            $query->where('user_activities.user_roles', 'like', '%' . $filters['user_roles'] . '%');
        }
        if (! empty($filters['country_name'])) {
            $query->where('countries.name', $filters['country_name']);
        }
        if (! empty($filters['activity_type'])) {
            $query->where('user_activities.activity_type', $filters['activity_type']);
        }
        if (! empty($filters['date_from'])) {
            $query->whereDate('user_activities.activity_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('user_activities.activity_date', '<=', $filters['date_to']);
        }

        return $query
            ->select('user_activities.*')
            ->addSelect('countries.name as profile_country_name')
            ->addSelect('countries.code as profile_country_code')
            ->orderBy('user_activities.id', 'desc');
    }

    public function mapActivityCountry($activity)
    {
        $activity->country_name = $activity->profile_country_name;
        $activity->country_code = $activity->profile_country_code;
        unset($activity->profile_country_name, $activity->profile_country_code);

        return $activity;
    }

    public function start(int $userId, array $filters): array
    {
        $filters = $this->sanitizeFilters($filters);
        $total = $filters === []
            ? UserActivity::count()
            : $this->filteredQuery($filters)->count('user_activities.id');

        if ($total === 0) {
            return [
                'export_id' => null,
                'total' => 0,
            ];
        }

        $exportId = (string) Str::uuid();
        $filename = 'user_activities_' . date('Ymd_His') . '.csv';
        $path = 'activity-exports/' . $userId . '/' . $exportId . '.csv';

        Storage::disk('local')->put($path, "\xEF\xBB\xBF");

        $handle = fopen(Storage::disk('local')->path($path), 'a');
        fputcsv($handle, self::HEADINGS);
        fclose($handle);

        $this->putState($userId, $exportId, [
            'user_id' => $userId,
            'filters' => $filters,
            'path' => $path,
            'filename' => $filename,
            'total' => $total,
            'processed' => 0,
            'cursor_id' => null,
            'status' => 'running',
        ]);

        return [
            'export_id' => $exportId,
            'total' => $total,
        ];
    }

    public function processChunk(int $userId, string $exportId): array
    {
        $state = $this->requireState($userId, $exportId);

        if ($state['status'] === 'cancelled') {
            abort(409, 'Export cancelled.');
        }

        if ($state['status'] === 'completed') {
            return $this->progressPayload($state, true);
        }

        $query = $this->filteredQuery($state['filters']);
        if (! empty($state['cursor_id'])) {
            $query->where('user_activities.id', '<', $state['cursor_id']);
        }

        $rows = $query->limit(self::CHUNK_SIZE)->get();

        $handle = fopen(Storage::disk('local')->path($state['path']), 'a');
        $lastId = $state['cursor_id'];
        foreach ($rows as $row) {
            $row = $this->mapActivityCountry($row);
            fputcsv($handle, $this->mapRow($row));
            $lastId = $row->id;
        }
        fclose($handle);

        $state['processed'] += $rows->count();
        $state['cursor_id'] = $lastId;
        $done = $rows->count() < self::CHUNK_SIZE || $state['processed'] >= $state['total'];

        if ($done) {
            $state['status'] = 'completed';
            $state['processed'] = min($state['processed'], $state['total']);
        }

        $this->putState($userId, $exportId, $state);

        return $this->progressPayload($state, $done);
    }

    public function cancel(int $userId, string $exportId): array
    {
        $state = $this->requireState($userId, $exportId);
        $state['status'] = 'cancelled';
        $this->putState($userId, $exportId, $state);

        if (! empty($state['path'])) {
            Storage::disk('local')->delete($state['path']);
        }

        return ['status' => 'cancelled'];
    }

    public function download(int $userId, string $exportId): array
    {
        $state = $this->requireState($userId, $exportId);

        if ($state['status'] !== 'completed' || empty($state['path']) || ! Storage::disk('local')->exists($state['path'])) {
            abort(404);
        }

        return [
            'path' => Storage::disk('local')->path($state['path']),
            'filename' => $state['filename'],
        ];
    }

    private function mapRow($row): array
    {
        $activityDate = $row->activity_date instanceof \DateTimeInterface
            ? $row->activity_date->format('Y-m-d H:i:s')
            : $row->activity_date;

        return [
            $row->user_name,
            $row->email,
            $row->user_roles,
            $row->ecclesia_name,
            $row->ip,
            $row->country_code,
            $row->country_name,
            $row->device_type,
            $row->browser,
            $row->url,
            $row->activity_type,
            $activityDate,
        ];
    }

    private function sanitizeFilters(array $filters): array
    {
        return collect($filters)
            ->only([
                'user_name',
                'email',
                'user_roles',
                'country_name',
                'activity_type',
                'date_from',
                'date_to',
            ])
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            })
            ->all();
    }

    private function progressPayload(array $state, bool $done): array
    {
        $total = (int) $state['total'];
        $processed = (int) $state['processed'];
        $percent = $total === 0 ? 100 : (int) min(100, round(($processed / $total) * 100));

        return [
            'export_id' => $state['export_id'] ?? null,
            'processed' => $processed,
            'total' => $total,
            'percent' => $percent,
            'done' => $done,
            'status' => $state['status'],
        ];
    }

    private function cacheKey(int $userId, string $exportId): string
    {
        return 'user_activity_export:' . $userId . ':' . $exportId;
    }

    private function putState(int $userId, string $exportId, array $state): void
    {
        $state['export_id'] = $exportId;
        Cache::put($this->cacheKey($userId, $exportId), $state, self::CACHE_TTL_SECONDS);
    }

    private function getState(int $userId, string $exportId): ?array
    {
        $state = Cache::get($this->cacheKey($userId, $exportId));

        return is_array($state) ? $state : null;
    }

    private function requireState(int $userId, string $exportId): array
    {
        $state = $this->getState($userId, $exportId);
        if (! $state) {
            abort(404, 'Export not found.');
        }

        return $state;
    }
}
