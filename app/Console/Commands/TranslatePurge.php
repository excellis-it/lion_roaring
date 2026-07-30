<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Drop cached translations so they are rebuilt correctly.
 *
 *   php artisan translate:purge --poisoned   # rows translated under a wrong source
 *   php artisan translate:purge --lang=hi    # one target language
 *   php artisan translate:purge --all
 *
 * `--poisoned` exists because the first implementation assumed every string was
 * English. Anything whose detected source is not English was therefore translated
 * from the wrong language and must be re-fetched; English rows are still valid.
 */
class TranslatePurge extends Command
{
    protected $signature = 'translate:purge
        {--poisoned : Drop rows whose source text is not English (wrong-source era)}
        {--lang= : Drop every row for one target language}
        {--all : Drop the entire translation cache}
        {--detections : Also drop cached language detections}
        {--force : Skip confirmation}';

    protected $description = 'Purge cached translations so they are rebuilt with the correct source language';

    public function handle(): int
    {
        $all = (bool) $this->option('all');
        $lang = (string) ($this->option('lang') ?? '');
        $poisoned = (bool) $this->option('poisoned');

        if (!$all && $lang === '' && !$poisoned) {
            $this->error('Choose one of --poisoned, --lang= or --all.');

            return self::FAILURE;
        }

        $query = DB::table('translation_cache');

        if ($all) {
            $label = 'the entire translation cache';
        } elseif ($lang !== '') {
            $query->where('target_lang', $lang);
            $label = "all rows for target '{$lang}'";
        } else {
            // Non-English source text — everything the wrong-source era corrupted.
            $query->whereIn('source_hash', function ($sub) {
                $sub->select('source_hash')
                    ->from('translation_source')
                    ->where('detected_lang', 'not like', 'en%');
            });
            $label = 'rows whose source text is not English';
        }

        $count = (clone $query)->count();
        if ($count === 0) {
            $this->info('Nothing to purge.');

            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm("Delete {$count} cached rows ({$label})?", true)) {
            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Deleted {$deleted} translation_cache rows.");

        if ($this->option('detections') || $all) {
            $d = DB::table('translation_source')->delete();
            $this->info("Deleted {$d} translation_source rows.");
        }

        Cache::forget('translate:allowed_langs');
        $this->newLine();
        $this->comment('Bump TRANSLATE_CACHE_VERSION in .env so visitors drop their localStorage copies too.');

        return self::SUCCESS;
    }
}
