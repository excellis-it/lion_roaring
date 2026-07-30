<?php

namespace App\Console\Commands;

use App\Services\GoogleTranslateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Pre-pay for a language so real visitors never wait on (or trigger) API calls.
 *
 *   php artisan translate:warm es fr --crawl
 *   php artisan translate:warm hi --from-cache
 *   php artisan translate:warm es --url=https://site.test/about-us --dry
 *
 * Running this once per language turns an unpredictable per-visitor trickle of
 * spend into a single, measurable, one-off cost.
 */
class TranslateWarm extends Command
{
    protected $signature = 'translate:warm
        {langs* : Target language codes}
        {--crawl : Crawl the default public route list for source strings}
        {--url=* : Extra URLs to crawl}
        {--from-cache : Reuse every source string already stored in translation_cache}
        {--limit=5000 : Maximum distinct strings to warm per language}
        {--dry : Show what would be sent and the estimated cost, then stop}';

    protected $description = 'Pre-populate the translation cache for one or more languages';

    /** Public pages that account for most first-visit strings. */
    private const DEFAULT_PATHS = [
        '/', '/about-us', '/contact-us', '/terms-and-conditions', '/privacy-policy',
        '/login', '/register', '/e-store', '/e-learning',
    ];

    public function handle(): int
    {
        if (!GoogleTranslateService::enabled()) {
            $this->error('Translation is disabled or GOOGLE_TRANSLATE_API_KEY is not set.');

            return self::FAILURE;
        }

        $corpus = $this->buildCorpus();
        if ($corpus === []) {
            $this->warn('No source strings collected. Use --crawl, --url= or --from-cache.');

            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0 && count($corpus) > $limit) {
            $corpus = array_slice($corpus, 0, $limit);
        }

        $chars = 0;
        foreach ($corpus as $text) {
            $chars += mb_strlen($text);
        }

        $langs = array_values(array_filter(array_map(
            fn ($l) => GoogleTranslateService::normalizeLangCode((string) $l),
            (array) $this->argument('langs')
        )));

        $this->info(sprintf('Corpus: %d distinct strings, %s characters.', count($corpus), number_format($chars)));

        foreach ($langs as $lang) {
            $uncached = $this->uncachedFor($corpus, $lang);
            $uncachedChars = 0;
            foreach ($uncached as $t) {
                $uncachedChars += mb_strlen($t);
            }

            $this->line(sprintf(
                '  %s → %d new strings, %s chars, est. $%s',
                $lang,
                count($uncached),
                number_format($uncachedChars),
                number_format($uncachedChars / 1000000 * 20, 2)
            ));

            if ($this->option('dry') || $uncached === []) {
                continue;
            }

            $bar = $this->output->createProgressBar(count($uncached));
            $bar->start();
            foreach (array_chunk($uncached, 100) as $chunk) {
                GoogleTranslateService::translateBatch($chunk, $lang, 'en');
                $bar->advance(count($chunk));
            }
            $bar->finish();
            $this->newLine();
        }

        if ($this->option('dry')) {
            $this->comment('Dry run — nothing was sent to Google.');
        }

        return self::SUCCESS;
    }

    /** @return array<int, string> */
    private function buildCorpus(): array
    {
        $texts = [];

        if ($this->option('from-cache')) {
            DB::table('translation_cache')
                ->select('source_text')
                ->distinct()
                ->orderBy('id')
                ->chunk(1000, function ($rows) use (&$texts) {
                    foreach ($rows as $row) {
                        $texts[] = (string) $row->source_text;
                    }
                });
            $this->line('Collected ' . count($texts) . ' strings from the existing cache.');
        }

        $urls = (array) $this->option('url');
        if ($this->option('crawl')) {
            foreach (self::DEFAULT_PATHS as $path) {
                $urls[] = rtrim(config('app.url'), '/') . $path;
            }
        }

        foreach (array_unique($urls) as $url) {
            $found = $this->extractFromUrl($url);
            $this->line(sprintf('  %s → %d strings', $url, count($found)));
            $texts = array_merge($texts, $found);
        }

        // Normalize + dedupe + drop anything we would refuse to send anyway.
        $seen = [];
        foreach ($texts as $t) {
            $norm = GoogleTranslateService::normalize($t);
            if (!GoogleTranslateService::isTranslatable($norm)) {
                continue;
            }
            $seen[GoogleTranslateService::hash($norm)] = $norm;
        }

        return array_values($seen);
    }

    /**
     * Extract translatable text from a page, honouring the same exclusions the
     * browser engine applies so the warmed corpus matches what visitors request.
     *
     * @return array<int, string>
     */
    private function extractFromUrl(string $url): array
    {
        try {
            $response = Http::timeout(25)->withoutVerifying()->get($url);
            if (!$response->successful()) {
                $this->warn("  {$url} → HTTP {$response->status()}");

                return [];
            }
        } catch (\Throwable $e) {
            $this->warn("  {$url} → {$e->getMessage()}");

            return [];
        }

        $html = $response->body();
        if (trim($html) === '') {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $skipTags = ['script', 'style', 'noscript', 'textarea', 'code', 'pre', 'svg', 'iframe', 'i'];
        $out = [];

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//text()') ?: [] as $node) {
            $value = trim($node->nodeValue ?? '');
            if ($value === '') {
                continue;
            }

            $skip = false;
            for ($p = $node->parentNode; $p instanceof \DOMElement; $p = $p->parentNode) {
                if (in_array(strtolower($p->tagName), $skipTags, true)) {
                    $skip = true;
                    break;
                }
                $class = (string) $p->getAttribute('class');
                if (str_contains($class, 'notranslate')
                    || $p->getAttribute('translate') === 'no'
                    || $p->hasAttribute('data-nt')
                    || preg_match('/(^|\s)(fa|fas|far|fab|bi|material-icons|material-symbols-outlined|icon)(\s|$)/', $class)) {
                    $skip = true;
                    break;
                }
            }

            if (!$skip) {
                $out[] = $value;
            }
        }

        // Attributes the browser engine also translates.
        foreach (['placeholder', 'title', 'aria-label', 'alt'] as $attr) {
            foreach ($xpath->query('//*[@' . $attr . ']') ?: [] as $el) {
                $value = trim($el->getAttribute($attr));
                if ($value !== '') {
                    $out[] = $value;
                }
            }
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $corpus
     * @return array<int, string>
     */
    private function uncachedFor(array $corpus, string $lang): array
    {
        $byHash = [];
        foreach ($corpus as $text) {
            $byHash[GoogleTranslateService::hash($text)] = $text;
        }

        foreach (array_chunk(array_keys($byHash), 500) as $chunk) {
            $hits = DB::table('translation_cache')
                ->where('target_lang', $lang)
                ->whereIn('source_hash', $chunk)
                ->pluck('source_hash');
            foreach ($hits as $hash) {
                unset($byHash[$hash]);
            }
        }

        return array_values($byHash);
    }
}
