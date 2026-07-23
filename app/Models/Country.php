<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'flag_image',
        'domain',
        'is_global',
        'status',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'status'    => 'boolean',
    ];

    /**
     * Get the languages associated with the country.
     */
    public function languages()
    {
        return $this->belongsToMany(TranslateLanguage::class, 'country_translate_language', 'country_id', 'translate_language_id');
    }

    /**
     * Check if this is the GLOBAL (main) entry.
     */
    public function isGlobal(): bool
    {
        return (bool) $this->is_global;
    }

    /**
     * Scope: only non-global (regional) countries.
     */
    public function scopeRegional($query)
    {
        return $query->where('is_global', false);
    }

    /**
     * Scope: only the global entry.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Static cache for countries with domains (loaded once per request).
     */
    private static $countriesWithDomains = null;

    /**
     * Load and cache all countries that have a domain set.
     * This runs only ONE query per request regardless of how many helpers call it.
     */
    private static function getCountriesWithDomains()
    {
        if (self::$countriesWithDomains === null) {
            self::$countriesWithDomains = static::whereNotNull('domain')
                ->where('domain', '!=', '')
                ->get();
        }
        return self::$countriesWithDomains;
    }

    /**
     * Reset cached domain list (useful in tests).
     */
    public static function clearDomainCache(): void
    {
        self::$countriesWithDomains = null;
    }

    /**
     * Normalize a URL path to a trimmed slash form, e.g. "/lion-roaring-us".
     */
    public static function normalizeUrlPath(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '' || $path === '/') {
            return '';
        }

        return '/' . trim($path, '/');
    }

    /**
     * Current request path including subdirectory mounts (e.g. /lion-roaring-us/us).
     */
    public static function currentRequestPath(): string
    {
        $uriPath = parse_url(request()->getRequestUri(), PHP_URL_PATH);

        return self::normalizeUrlPath($uriPath);
    }

    /**
     * Whether the current request matches a stored country domain URL.
     * Supports host-only domains and same-host path-based domains
     * (e.g. https://excellis.co.in/lion-roaring-us).
     */
    public static function requestMatchesDomainUrl(string $domainUrl, ?string $requestPath = null): bool
    {
        $parsed = parse_url($domainUrl);
        $domainHost = $parsed['host'] ?? null;
        if (!$domainHost || strtolower($domainHost) !== strtolower(request()->getHost())) {
            return false;
        }

        $domainPort = $parsed['port'] ?? null;
        $requestPort = request()->getPort();
        if ($domainPort !== null) {
            if ((string) $domainPort !== (string) $requestPort) {
                return false;
            }
        } elseif (!in_array((int) $requestPort, [80, 443], true)) {
            // Non-default ports without an explicit domain port do not match.
            return false;
        }

        $domainPath = self::normalizeUrlPath($parsed['path'] ?? '');
        $requestPath = self::normalizeUrlPath($requestPath ?? self::currentRequestPath());

        if ($domainPath === '') {
            return true;
        }

        return $requestPath === $domainPath
            || str_starts_with($requestPath . '/', $domainPath . '/');
    }

    /**
     * Specificity score for domain matches (longer path wins over host-only).
     */
    public static function domainMatchScore(string $domainUrl): int
    {
        $parsed = parse_url($domainUrl);
        $path = self::normalizeUrlPath($parsed['path'] ?? '');

        return $path === '' ? 0 : strlen($path);
    }

    /**
     * Find a country by matching the current request URL against stored domains.
     * Uses host + optional path; prefers the longest/most specific path match
     * so same-host demo installs (…/lion-roaring-org vs …/lion-roaring-us) work.
     */
    public static function findByCurrentRequest(): ?self
    {
        $countries = self::getCountriesWithDomains();
        $requestPath = self::currentRequestPath();

        $best = null;
        $bestScore = -1;

        foreach ($countries as $country) {
            $domain = trim((string) $country->domain);
            if ($domain === '' || !self::requestMatchesDomainUrl($domain, $requestPath)) {
                continue;
            }

            $score = self::domainMatchScore($domain);
            if ($score > $bestScore) {
                $best = $country;
                $bestScore = $score;
            }
        }

        return $best;
    }

    /**
     * Get a country's domain URL by code (from the cached collection).
     * Returns null if no domain is set for that code.
     */
    public static function getDomainByCode(string $code): ?string
    {
        $code = strtoupper($code);
        $countries = self::getCountriesWithDomains();

        foreach ($countries as $country) {
            if (strtoupper($country->code) === $code) {
                return $country->domain;
            }
        }

        return null;
    }

    /**
     * Get the GLOBAL country entry's domain (from the cached collection).
     */
    public static function getGlobalDomain(): ?string
    {
        $countries = self::getCountriesWithDomains();

        foreach ($countries as $country) {
            if ($country->is_global) {
                return $country->domain;
            }
        }

        return null;
    }
}
