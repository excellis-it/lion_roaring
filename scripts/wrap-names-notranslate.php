<?php

/**
 * One-off: wrap person-name Blade echoes with no_translate() for Google Translate exclusion.
 * Run: php scripts/wrap-names-notranslate.php
 */

$root = dirname(__DIR__) . '/resources/views';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$skipLineHints = [
    'value=',
    'placeholder=',
    'data-',
    'title=',
    'alt=',
    'aria-',
    'old(',
    'name="',
    "name='",
    'data-column_name=',
    'data-sorting',
];

$nameExpr = '(?:'
    . '[^}]*?(?:->|::|\?)full_name[^}]*'
    . '|'
    . '[^}]*?getFullNameAttribute\(\)[^}]*'
    . '|'
    . '[^}]*?(?:->|::|\?)user_name[^}]*'
    . ')';

$pattern = '/\{\{\s*(' . $nameExpr . ')\s*\}\}/';

$updatedFiles = 0;
$replacements = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    // Skip pure form create blades? Still wrap display titles there.

    $original = file_get_contents($path);
    $lines = preg_split("/\r\n|\n|\r/", $original);
    $changed = false;

    foreach ($lines as $i => $line) {
        if (! str_contains($line, 'full_name')
            && ! str_contains($line, 'getFullNameAttribute')
            && ! str_contains($line, 'user_name')) {
            continue;
        }

        // Skip attribute / form binding lines (would break HTML if we inject a <span>).
        $skip = false;
        foreach ($skipLineHints as $hint) {
            if (stripos($line, $hint) !== false) {
                // Allow when the name echo is clearly separate text content after a closing quote tag.
                // e.g. <option value="{{ $user->id }}">{{ $user->full_name }}
                if (preg_match('/{{[^}]*->(?:full_name|user_name|getFullNameAttribute)/', $line)
                    && preg_match('/"[^"]*"\s*>/', $line)) {
                    // partially allow — handle carefully below
                    continue;
                }
                if (preg_match('/\b(value|placeholder|title|alt|aria-|data-[\w-]*)\s*=\s*["\'][^"\']*\{\{/', $line)
                    || preg_match('/\{\{\s*old\(/', $line)
                    || str_contains($line, 'data-column_name=')) {
                    $skip = true;
                    break;
                }
            }
        }
        if ($skip) {
            continue;
        }

        // Already wrapped
        if (str_contains($line, 'no_translate(')) {
            continue;
        }

        $newLine = preg_replace_callback($pattern, function ($m) use ($line) {
            $expr = trim($m[1]);

            // Skip if this match sits inside an HTML attribute on the same line.
            $pos = strpos($line, $m[0]);
            if ($pos === false) {
                return $m[0];
            }
            $before = substr($line, 0, $pos);
            if (preg_match('/(?:value|placeholder|title|alt|aria-[\w-]*|data-[\w-]*)\s*=\s*["\'][^"\']*$/', $before)) {
                return $m[0];
            }

            return '{!! no_translate(' . $expr . ') !!}';
        }, $line, -1, $count);

        if ($count > 0 && $newLine !== $line) {
            $lines[$i] = $newLine;
            $changed = true;
            $replacements += $count;
        }
    }

    // first_name + last_name display pairs (same line, simple forms)
    foreach ($lines as $i => $line) {
        if (str_contains($line, 'no_translate(')) {
            // still may have first/last unwrapped
        }
        if (! str_contains($line, 'first_name') || ! str_contains($line, 'last_name')) {
            continue;
        }
        if (str_contains($line, 'value=') || str_contains($line, 'old(') || str_contains($line, 'name="') || str_contains($line, "name='")) {
            if (! preg_match('/"[^"]*"\s*>.*first_name/', $line)) {
                continue;
            }
        }
        if (str_contains($line, 'no_translate(') && preg_match('/no_translate\([^)]*first_name/', $line)) {
            continue;
        }

        // {{ $x->first_name }} {{ $x->last_name }}
        $pairPattern = '/\{\{\s*([^\{\}]*?->first_name)\s*\}\}\s*\{\{\s*([^\{\}]*?->last_name)\s*\}\}/';
        $newLine = preg_replace_callback($pairPattern, function ($m) use ($line) {
            $pos = strpos($line, $m[0]);
            $before = $pos === false ? '' : substr($line, 0, $pos);
            if (preg_match('/(?:value|placeholder|title|alt|data-[\w-]*)\s*=\s*["\'][^"\']*$/', $before)) {
                return $m[0];
            }
            $first = trim($m[1]);
            $last = trim($m[2]);
            return '{!! no_translate(trim((' . $first . ' ?? \'\') . \' \' . (' . $last . ' ?? \'\'))) !!}';
        }, $line, -1, $count);

        if ($count > 0 && $newLine !== $line) {
            $lines[$i] = $newLine;
            $changed = true;
            $replacements += $count;
        }

        // Auth::user()->first_name }} {{ Auth::user()->last_name
        $authPair = '/\{\{\s*(Auth::user\(\)->first_name)\s*\}\}\s*\{\{\s*(Auth::user\(\)->last_name)\s*\}\}/';
        $newLine = preg_replace_callback($authPair, function ($m) {
            return '{!! no_translate(trim((' . $m[1] . ' ?? \'\') . \' \' . (' . $m[2] . ' ?? \'\'))) !!}';
        }, $lines[$i], -1, $count2);
        if ($count2 > 0 && $newLine !== $lines[$i]) {
            $lines[$i] = $newLine;
            $changed = true;
            $replacements += $count2;
        }
    }

    if ($changed) {
        file_put_contents($path, implode("\n", $lines) . (str_ends_with($original, "\n") ? "\n" : ''));
        $updatedFiles++;
        echo 'Updated: ' . substr($path, strlen(dirname(__DIR__)) + 1) . PHP_EOL;
    }
}

echo "Done. Files: {$updatedFiles}, replacements: {$replacements}" . PHP_EOL;
