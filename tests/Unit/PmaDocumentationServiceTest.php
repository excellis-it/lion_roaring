<?php

namespace Tests\Unit;

use App\Services\PmaDocumentationService;
use Tests\TestCase;

class PmaDocumentationServiceTest extends TestCase
{
    public function test_parse_front_matter_extracts_meta_and_body(): void
    {
        $raw = <<<'MD'
---
title: Messaging
updated: 2026-07-24
status: coming_soon
sidebar_key: messaging
---

# Messaging

Hello
MD;

        $service = new PmaDocumentationService();
        [$meta, $body] = $service->parseFrontMatter($raw);

        $this->assertSame('Messaging', $meta['title']);
        $this->assertSame('2026-07-24', $meta['updated']);
        $this->assertSame('coming_soon', $meta['status']);
        $this->assertSame('messaging', $meta['sidebar_key']);
        $this->assertStringContainsString('# Messaging', $body);
        $this->assertStringContainsString('Hello', $body);
    }

    public function test_render_markdown_converts_heading(): void
    {
        $service = new PmaDocumentationService();
        $html = $service->renderMarkdown("# Hello\n\nWorld");

        $this->assertStringContainsString('<h1>', $html);
        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('<p>World</p>', $html);
    }

    public function test_find_section_returns_null_for_unknown_slug(): void
    {
        $service = new PmaDocumentationService();
        $this->assertNull($service->findSection('does-not-exist'));
    }

    public function test_load_document_reads_index(): void
    {
        $service = new PmaDocumentationService();
        $doc = $service->loadDocument('index.md');

        $this->assertSame('ready', $doc['meta']['status'] ?? null);
        $this->assertNotEmpty($doc['html']);
        $this->assertStringContainsString('PMA', $doc['html']);
    }
}
