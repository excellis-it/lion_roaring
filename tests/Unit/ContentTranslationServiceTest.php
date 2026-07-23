<?php

namespace Tests\Unit;

use App\Services\ContentTranslationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentTranslationServiceTest extends TestCase
{
    public function test_resolve_target_language_null_on_first_load(): void
    {
        $this->assertNull(ContentTranslationService::resolveTargetLanguage(null));
        $this->assertNull(ContentTranslationService::resolveTargetLanguage(''));
        $this->assertNull(ContentTranslationService::resolveTargetLanguage(null, null));
        $this->assertNull(ContentTranslationService::resolveTargetLanguage(null, ''));
    }

    public function test_resolve_target_language_from_googtrans_cookie(): void
    {
        $this->assertSame('de', ContentTranslationService::resolveTargetLanguage('/auto/de'));
        $this->assertSame('es', ContentTranslationService::resolveTargetLanguage('/auto/es'));
    }

    public function test_resolve_target_language_from_content_lang_cookie(): void
    {
        $this->assertSame('en', ContentTranslationService::resolveTargetLanguage(null, 'en'));
        $this->assertSame('de', ContentTranslationService::resolveTargetLanguage(null, 'de'));
    }

    public function test_googtrans_takes_priority_over_content_lang(): void
    {
        $this->assertSame('de', ContentTranslationService::resolveTargetLanguage('/auto/de', 'en'));
    }

    public function test_translate_returns_empty_unchanged(): void
    {
        Http::fake();
        $this->assertSame('', ContentTranslationService::translate('', 'en'));
        $this->assertSame('   ', ContentTranslationService::translate('   ', 'en'));
        Http::assertNothingSent();
    }

    public function test_translate_uses_cache_and_http(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::response([
                [['Royal garment', 'vestido Real', null, null, 3]],
                null,
                'es',
            ], 200),
        ]);

        $first = ContentTranslationService::translate('vestido Real', 'en');
        $this->assertSame('Royal garment', $first);

        $second = ContentTranslationService::translate('vestido Real', 'en');
        $this->assertSame('Royal garment', $second);

        Http::assertSentCount(1);
    }

    public function test_translate_falls_back_to_original_on_http_failure(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::response('error', 500),
        ]);

        $out = ContentTranslationService::translate('Hola mundo', 'en');
        $this->assertSame('Hola mundo', $out);
    }

    public function test_translate_bulletin_fields_mutates_title_and_description(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::sequence()
                ->push([[['Title EN', 'Titulo', null, null, 3]], null, 'es'], 200)
                ->push([[['Body EN', 'Cuerpo', null, null, 3]], null, 'es'], 200),
        ]);

        $bulletin = (object) ['title' => 'Titulo', 'description' => 'Cuerpo'];
        ContentTranslationService::translateBulletinFields($bulletin, 'en');

        $this->assertSame('Title EN', $bulletin->title);
        $this->assertSame('Body EN', $bulletin->description);
    }

    public function test_translate_many_uses_parallel_http_once_per_unique_miss(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::sequence()
                ->push([[['One', 'Uno', null, null, 3]], null, 'es'], 200)
                ->push([[['Two', 'Dos', null, null, 3]], null, 'es'], 200),
        ]);

        $out = ContentTranslationService::translateMany([
            'a' => 'Uno',
            'b' => 'Dos',
        ], 'en');

        $this->assertSame('One', $out['a']);
        $this->assertSame('Two', $out['b']);
        Http::assertSentCount(2);
    }

    public function test_translate_many_falls_back_when_pool_returns_exception(): void
    {
        Cache::flush();
        Http::fake(function () {
            throw new \GuzzleHttp\Exception\ConnectException(
                'cURL error 28: Connection timed out',
                new \GuzzleHttp\Psr7\Request('GET', 'https://translate.googleapis.com/translate_a/single')
            );
        });

        $out = ContentTranslationService::translateMany([
            'a' => 'Hola',
        ], 'en');

        $this->assertSame('Hola', $out['a']);
    }
}
