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
     * Find a country by matching the current request URL against stored domains.
     * Uses the cached collection — no extra DB query.
     */
    public static function findByCurrentRequest(): ?self
    {
        $requestHost = request()->getHost();
        $requestPort = request()->getPort();

        $countries = self::getCountriesWithDomains();

        foreach ($countries as $country) {
            $parsed = parse_url($country->domain);
            $domainHost = $parsed['host'] ?? null;
            $domainPort = $parsed['port'] ?? null;

            if ($domainHost === $requestHost) {
                // If domain has port specified, match port too
                if ($domainPort !== null) {
                    if ((string) $domainPort === (string) $requestPort) {
                        return $country;
                    }
                } else {
                    // No port in domain — match if request uses default port
                    if (in_array($requestPort, [80, 443])) {
                        return $country;
                    }
                }
            }
        }

        return null;
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
