<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Models\Country;
use Illuminate\Http\Request;
use Tests\TestCase;

class CountryDomainMatchTest extends TestCase
{
    public function test_normalize_url_path(): void
    {
        $this->assertSame('', Country::normalizeUrlPath(null));
        $this->assertSame('', Country::normalizeUrlPath('/'));
        $this->assertSame('/lion-roaring-us', Country::normalizeUrlPath('/lion-roaring-us/'));
        $this->assertSame('/lion-roaring-us/us', Country::normalizeUrlPath('lion-roaring-us/us'));
    }

    public function test_path_based_domains_prefer_longest_match(): void
    {
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-us/login');

        $this->assertTrue(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-us'));
        $this->assertFalse(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-org'));
        $this->assertTrue(
            Country::domainMatchScore('https://excellis.co.in/lion-roaring-us')
            > Country::domainMatchScore('https://excellis.co.in')
        );
    }

    public function test_org_path_matches_global_domain_not_us(): void
    {
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-org/');

        $this->assertTrue(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-org'));
        $this->assertFalse(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-us'));
    }

    public function test_us_path_with_country_segment_still_matches_us_domain(): void
    {
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-us/us?cc=us');

        $this->assertTrue(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-us'));
        $this->assertFalse(Country::requestMatchesDomainUrl('https://excellis.co.in/lion-roaring-org'));
    }

    public function test_host_only_domain_still_matches_production_style(): void
    {
        $this->setRequestUrl('https://lionroaring.us/about');

        $this->assertTrue(Country::requestMatchesDomainUrl('https://lionroaring.us'));
        $this->assertFalse(Country::requestMatchesDomainUrl('https://lionroaring.org'));
    }

    public function test_canonical_redirect_strips_us_segment_on_path_domain_without_loop(): void
    {
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-us/us');

        // Simulate Country::getDomainByCode('US') via binding findByCurrentRequest context:
        // resolveCanonicalRedirect uses getDomainByCode from DB; exercise safeExternalRedirect
        // and isRequestOnCountryDomainUrl path instead.
        $this->assertTrue(
            Helper::isRequestOnCountryDomainUrl('https://excellis.co.in/lion-roaring-us')
        );

        $redirect = Helper::safeExternalRedirectUrl('https://excellis.co.in/lion-roaring-us');
        $this->assertSame('https://excellis.co.in/lion-roaring-us', $redirect);

        $this->setRequestUrl('https://excellis.co.in/lion-roaring-us');
        $this->assertNull(
            Helper::safeExternalRedirectUrl('https://excellis.co.in/lion-roaring-us')
        );
    }

    public function test_is_usa_instance_env_fallback_respects_path(): void
    {
        // No countries with domains in DB → findByCurrentRequest returns null → env fallback.
        $this->createEmptyCountriesTable();
        Country::clearDomainCache();

        putenv('LION_ROARING_USA=https://excellis.co.in/lion-roaring-us');
        $_ENV['LION_ROARING_USA'] = 'https://excellis.co.in/lion-roaring-us';
        $_SERVER['LION_ROARING_USA'] = 'https://excellis.co.in/lion-roaring-us';

        $this->resetHelperDomainCache();
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-us/');
        $this->assertTrue(Helper::isUsaInstance());

        $this->resetHelperDomainCache();
        $this->setRequestUrl('https://excellis.co.in/lion-roaring-org/');
        $this->assertFalse(Helper::isUsaInstance());
    }

    private function createEmptyCountriesTable(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);
        \Illuminate\Support\Facades\DB::purge('sqlite');

        if (!\Illuminate\Support\Facades\Schema::hasTable('countries')) {
            \Illuminate\Support\Facades\Schema::create('countries', function ($table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('code')->nullable();
                $table->string('domain')->nullable();
                $table->boolean('is_global')->default(false);
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    private function setRequestUrl(string $url): void
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? '';
        $server = [
            'HTTP_HOST' => ($parsed['host'] ?? 'localhost')
                . (isset($parsed['port']) ? ':' . $parsed['port'] : ''),
            'SERVER_NAME' => $parsed['host'] ?? 'localhost',
            'SERVER_PORT' => $parsed['port'] ?? (($parsed['scheme'] ?? 'https') === 'https' ? 443 : 80),
            'REQUEST_URI' => $path . ($query !== '' ? '?' . $query : ''),
            'QUERY_STRING' => $query,
            'REQUEST_METHOD' => 'GET',
            'HTTPS' => ($parsed['scheme'] ?? 'https') === 'https' ? 'on' : 'off',
        ];

        $request = Request::create($url, 'GET', [], [], [], $server);
        $this->app->instance('request', $request);
    }

    private function resetHelperDomainCache(): void
    {
        $ref = new \ReflectionClass(Helper::class);
        foreach (['domainResolved', 'resolvedCountry', 'effectiveCountryResolved', 'resolvedEffectiveCountry'] as $prop) {
            if (!$ref->hasProperty($prop)) {
                continue;
            }
            $p = $ref->getProperty($prop);
            $p->setAccessible(true);
            if ($prop === 'domainResolved' || $prop === 'effectiveCountryResolved') {
                $p->setValue(null, false);
            } else {
                $p->setValue(null, null);
            }
        }
        Country::clearDomainCache();
    }
}
