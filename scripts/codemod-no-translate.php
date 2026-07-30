<?php

/**
 * One-time codemod: wrap person-name echoes in no_translate().
 *
 * A DOM walker cannot tell `{{ $user->first_name }}` from ordinary prose once the
 * page is rendered, so the exclusion has to be marked in the source. This finds
 * name echoes in *text* position and rewrites them; attribute values, JS strings
 * and <script> blocks are left alone because no_translate() emits a <span>.
 *
 *   php scripts/codemod-no-translate.php            # dry run, prints the diff
 *   php scripts/codemod-no-translate.php --apply    # write changes
 */

$root = dirname(__DIR__);
$apply = in_array('--apply', $argv, true);

// Only surfaces that are actually translated. Admin is English-only; emails are
// already hand-wrapped and are never processed by the client engine.
$scanDirs = [
    'resources/views/frontend',
    'resources/views/user',
    'resources/views/ecom',
    'resources/views/elearning',
    'resources/views/chatbot',
    'resources/views/components',
];

// `->name` is deliberately absent: categories, products and countries all use it
// and those genuinely should be translated.
$namePattern = '/(?:->|\[[\'"])(?:full_name|first_name|last_name|middle_name|user_name|username)(?:[\'"]\])?|getFullNameAttribute\s*\(/';

$files = [];
foreach ($scanDirs as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $files[] = $file->getPathname();
        }
    }
}
sort($files);

$totalEdits = 0;
$touchedFiles = 0;
$skippedContext = 0;

foreach ($files as $file) {
    $source = file_get_contents($file);
    if ($source === false || !preg_match($namePattern, $source)) {
        continue;
    }

    $scriptRanges = collectScriptRanges($source);
    $edits = [];

    // Match plain {{ ... }} echoes only. {!! !!} is already raw and usually
    // intentional; {{-- --}} comments are excluded by the negative lookahead.
    if (preg_match_all('/\{\{(?!--)(.+?)\}\}/s', $source, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $i => [$full, $offset]) {
            $expr = trim($matches[1][$i][0]);

            if ($expr === '' || !preg_match($namePattern, $expr)) {
                continue;
            }
            if (str_contains($expr, 'no_translate') || str_contains($expr, 'noTranslate')) {
                continue;
            }
            // Blade echoes that are really control flow or assignment
            if (str_contains($expr, '=') && !preg_match('/[=!<>]=|=>/', $expr)) {
                continue;
            }
            if (inRanges($offset, $scriptRanges)) {
                $skippedContext++;
                continue;
            }
            if (insideHtmlTag($source, $offset) || insideQuotedString($source, $offset)) {
                $skippedContext++;
                continue;
            }

            $edits[] = [
                'offset' => $offset,
                'length' => strlen($full),
                'from' => $full,
                'to' => '{!! no_translate(' . $expr . ') !!}',
            ];
        }
    }

    if ($edits === []) {
        continue;
    }

    // Apply back-to-front so earlier offsets stay valid.
    $updated = $source;
    foreach (array_reverse($edits) as $edit) {
        $updated = substr_replace($updated, $edit['to'], $edit['offset'], $edit['length']);
    }

    $rel = substr($file, strlen($root) + 1);
    echo "\n" . $rel . ' (' . count($edits) . ")\n";
    foreach ($edits as $edit) {
        echo '  - ' . trim($edit['from']) . "\n";
        echo '  + ' . trim($edit['to']) . "\n";
    }

    if ($apply) {
        file_put_contents($file, $updated);
    }

    $totalEdits += count($edits);
    $touchedFiles++;
}

echo "\n" . str_repeat('─', 60) . "\n";
echo ($apply ? 'APPLIED' : 'DRY RUN') . ": {$totalEdits} echoes in {$touchedFiles} files\n";
echo "Skipped (attribute / JS / script context): {$skippedContext}\n";
if (!$apply && $totalEdits > 0) {
    echo "Re-run with --apply to write.\n";
}

// ── context helpers ─────────────────────────────────────────────────────────

/** @return array<int, array{0:int,1:int}> */
function collectScriptRanges(string $source): array
{
    $ranges = [];
    foreach (['script', 'style'] as $tag) {
        $cursor = 0;
        while (($open = stripos($source, '<' . $tag, $cursor)) !== false) {
            $openEnd = strpos($source, '>', $open);
            if ($openEnd === false) {
                break;
            }
            $close = stripos($source, '</' . $tag, $openEnd);
            if ($close === false) {
                $ranges[] = [$open, strlen($source)];
                break;
            }
            $ranges[] = [$open, $close];
            $cursor = $close + 1;
        }
    }

    return $ranges;
}

/** @param array<int, array{0:int,1:int}> $ranges */
function inRanges(int $offset, array $ranges): bool
{
    foreach ($ranges as [$start, $end]) {
        if ($offset >= $start && $offset <= $end) {
            return true;
        }
    }

    return false;
}

/** True when the echo sits inside a tag's attribute area: `<td title="{{ ... }}">`. */
function insideHtmlTag(string $source, int $offset): bool
{
    $lastOpen = strrpos(substr($source, 0, $offset), '<');
    if ($lastOpen === false) {
        return false;
    }
    $lastClose = strrpos(substr($source, 0, $offset), '>');

    return $lastClose === false || $lastOpen > $lastClose;
}

/** True when the echo sits inside a quoted string on its own line (JS/PHP literals). */
function insideQuotedString(string $source, int $offset): bool
{
    $lineStart = strrpos(substr($source, 0, $offset), "\n");
    $lineStart = $lineStart === false ? 0 : $lineStart + 1;
    $before = substr($source, $lineStart, $offset - $lineStart);

    return (substr_count($before, '"') % 2 === 1) || (substr_count($before, "'") % 2 === 1);
}
