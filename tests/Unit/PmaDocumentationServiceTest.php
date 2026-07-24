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

    public function test_render_markdown_converts_gfm_tables(): void
    {
        $service = new PmaDocumentationService();
        $html = $service->renderMarkdown("| Area | Surface |\n|------|--------|\n| Shop | /e-store |");

        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>', $html);
        $this->assertStringContainsString('Shop', $html);
        $this->assertStringContainsString('/e-store', $html);
    }

    public function test_find_section_returns_null_for_unknown_slug(): void
    {
        $service = new PmaDocumentationService();
        $this->assertNull($service->findSection('does-not-exist'));
        $this->assertNull($service->findHub('does-not-exist'));
        $this->assertNull($service->findEntry('does-not-exist'));
    }

    public function test_hubs_include_five_product_surfaces(): void
    {
        $service = new PmaDocumentationService();
        $slugs = array_column($service->hubs(), 'slug');

        $this->assertSame([
            'website-frontend',
            'user-pma',
            'e-learning',
            'e-store',
            'mobile-app',
        ], $slugs);
    }

    public function test_find_entry_resolves_hub_and_section(): void
    {
        $service = new PmaDocumentationService();

        $this->assertSame('user-pma', $service->findHub('user-pma')['slug'] ?? null);
        $this->assertSame('messaging', $service->findSection('messaging')['slug'] ?? null);
        $this->assertSame('website-frontend', $service->findEntry('website-frontend')['slug'] ?? null);
        $this->assertSame('global-regional-domains', $service->findEntry('global-regional-domains')['slug'] ?? null);
    }

    public function test_load_document_reads_index(): void
    {
        $service = new PmaDocumentationService();
        $doc = $service->loadDocument('index.md');

        $this->assertSame('ready', $doc['meta']['status'] ?? null);
        $this->assertNotEmpty($doc['html']);
        $this->assertStringContainsString('PMA', $doc['html']);
    }

    public function test_load_document_reads_domain_rules(): void
    {
        $service = new PmaDocumentationService();
        $doc = $service->loadDocument('global-regional-domains.md');

        $this->assertStringContainsString('<table>', $doc['html']);
        $this->assertStringContainsString('Super Admin', $doc['html']);
        $this->assertStringContainsString('MAIN_URL', $doc['html']);
    }
}
