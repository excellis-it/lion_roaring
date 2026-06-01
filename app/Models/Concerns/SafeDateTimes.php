<?php

namespace App\Models\Concerns;

use Carbon\Carbon;
use DateTimeZone;

trait SafeDateTimes
{
    protected function resolveUserTimezone(?string $tz): string
    {
        $aliases = [
            'Asia/Calcutta' => 'Asia/Kolkata',
        ];

        $tz = $aliases[$tz] ?? $tz;

        return in_array($tz, DateTimeZone::listIdentifiers(), true)
            ? $tz
            : config('app.timezone');
    }

    /**
     * Fix common bad years (e.g. 20026) and trim input before parse/save.
     */
    protected function sanitizeStoredDateTime(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^(\d{4,})(-\d{2}-\d{2}[T ].*)/', $value, $matches)) {
            $yearPart = $matches[1];
            if (strlen($yearPart) > 4) {
                $fixedYear = '20' . substr($yearPart, 3);
                if ((int) $fixedYear >= 1900 && (int) $fixedYear <= 2100) {
                    $value = $fixedYear . substr($value, strlen($yearPart));
                } else {
                    $fixedYear = substr($yearPart, 0, 4);
                    if ((int) $fixedYear >= 1900 && (int) $fixedYear <= 2100) {
                        $value = $fixedYear . substr($value, strlen($yearPart));
                    }
                }
            }
        }

        return $value;
    }

    protected function parseStoredDateTime($value, ?string $recordTimezone = null): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            $value = $value->format('Y-m-d H:i:s');
        }

        $value = $this->sanitizeStoredDateTime((string) $value);
        if ($value === null) {
            return null;
        }

        $displayTz = auth()->check()
            ? $this->resolveUserTimezone(auth()->user()->time_zone)
            : config('app.timezone');

        $sourceTz = $recordTimezone !== null
            ? $this->resolveUserTimezone($recordTimezone)
            : config('app.timezone');

        try {
            return Carbon::parse($value, $sourceTz)->timezone($displayTz);
        } catch (\Throwable $e) {
            try {
                return Carbon::parse(str_replace('T', ' ', $value))->timezone($displayTz);
            } catch (\Throwable $e) {
                return null;
            }
        }
    }

    /**
     * Normalize request/DB values for storage (keeps API / datetime-local compatible input).
     */
    protected function normalizeDateTimeInput($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        $value = $this->sanitizeStoredDateTime(trim((string) $value));
        if ($value === null) {
            return null;
        }

        try {
            return Carbon::parse(str_replace('T', ' ', $value))->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
