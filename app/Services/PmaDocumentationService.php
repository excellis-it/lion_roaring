<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;
use RuntimeException;

class PmaDocumentationService
{
    public function sections(): array
    {
        return config('pma_documentation.sections', []);
    }

    public function findSection(string $slug): ?array
    {
        foreach ($this->sections() as $section) {
            if (($section['slug'] ?? null) === $slug) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @return array{meta: array<string, string>, html: string, body_markdown: string}
     */
    public function loadDocument(string $relativeFile): array
    {
        $base = rtrim((string) config('pma_documentation.base_path', base_path('docs/pma')), DIRECTORY_SEPARATOR);
        $path = $base.DIRECTORY_SEPARATOR.ltrim($relativeFile, DIRECTORY_SEPARATOR);

        $realBase = realpath($base);
        $realPath = realpath($path);

        if ($realBase === false || $realPath === false || ! str_starts_with($realPath, $realBase) || ! File::isFile($realPath)) {
            throw new RuntimeException('Documentation file not found: '.$relativeFile);
        }

        $raw = File::get($realPath);
        [$meta, $body] = $this->parseFrontMatter($raw);

        return [
            'meta' => $meta,
            'html' => $this->renderMarkdown($body),
            'body_markdown' => $body,
        ];
    }

    /**
     * @return array{0: array<string, string>, 1: string}
     */
    public function parseFrontMatter(string $raw): array
    {
        if (! preg_match('/\A---\s*\R(.*?)\R---\s*\R?(.*)\z/s', $raw, $matches)) {
            return [[], $raw];
        }

        $meta = [];
        foreach (preg_split('/\R/', trim($matches[1])) as $line) {
            if ($line === '' || ! str_contains($line, ':')) {
                continue;
            }
            [$key, $value] = explode(':', $line, 2);
            $meta[trim($key)] = trim($value);
        }

        return [$meta, $matches[2]];
    }

    public function renderMarkdown(string $markdown): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return (string) $converter->convert($markdown);
    }
}
