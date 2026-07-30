<?php

namespace App\Console\Commands;

use App\Services\GoogleTranslateService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * What translation has actually cost, and how much of the budget is left.
 *
 *   php artisan translate:stats
 *   php artisan translate:stats --month=2026-07
 */
class TranslateStats extends Command
{
    protected $signature = 'translate:stats {--month= : YYYY-MM, defaults to the current month}';

    protected $description = 'Show translation cache size, character usage and estimated spend';

    private const USD_PER_MILLION_CHARS = 20;

    public function handle(): int
    {
        $month = (string) ($this->option('month') ?: Carbon::now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $this->info('Translation usage — ' . $start->format('F Y'));

        $rows = DB::table('translation_usage')
            ->selectRaw('target_lang, SUM(billed_chars) AS chars, SUM(api_requests) AS requests, SUM(cache_hits) AS hits')
            ->whereBetween('usage_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('target_lang')
            ->orderByDesc('chars')
            ->get();

        if ($rows->isEmpty()) {
            $this->line('  No API usage recorded this month.');
        } else {
            $this->table(
                ['Language', 'Billed chars', 'API calls', 'Cache hits', 'Cost (USD)'],
                $rows->map(fn ($r) => [
                    $r->target_lang,
                    number_format((int) $r->chars),
                    number_format((int) $r->requests),
                    number_format((int) $r->hits),
                    '$' . number_format((int) $r->chars / 1000000 * self::USD_PER_MILLION_CHARS, 2),
                ])->all()
            );
        }

        $used = (int) $rows->sum('chars');
        $limit = GoogleTranslateService::monthlyCharLimit();
        $pct = $limit > 0 ? round($used / $limit * 100, 1) : 0;

        $this->newLine();
        $this->line(sprintf(
            'Budget: %s / %s chars (%s%%) — $%s of $%s',
            number_format($used),
            number_format($limit),
            $pct,
            number_format($used / 1000000 * self::USD_PER_MILLION_CHARS, 2),
            number_format($limit / 1000000 * self::USD_PER_MILLION_CHARS, 2)
        ));

        if ($pct >= 90) {
            $this->warn('Budget almost exhausted — new strings will stop being translated.');
        }

        $cache = DB::table('translation_cache')
            ->selectRaw('COUNT(*) AS rows_count, COUNT(DISTINCT source_hash) AS strings, COUNT(DISTINCT target_lang) AS langs, SUM(char_count) AS chars')
            ->first();

        $this->newLine();
        $this->info('Permanent cache');
        $this->line(sprintf(
            '  %s rows · %s distinct strings · %s languages · %s source chars stored',
            number_format((int) ($cache->rows_count ?? 0)),
            number_format((int) ($cache->strings ?? 0)),
            number_format((int) ($cache->langs ?? 0)),
            number_format((int) ($cache->chars ?? 0))
        ));
        $this->line('  Every row here is a string that will never be paid for again.');

        return self::SUCCESS;
    }
}
