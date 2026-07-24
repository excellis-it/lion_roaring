# PMA Super Admin Documentation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship a Super Admin–only Documentation menu (last sidebar item) with a modern hybrid Markdown-powered docs UI and an always-apply Cursor rule so agents announce and update docs when PMA features change.

**Architecture:** Markdown files live in `docs/pma/`. `config/pma_documentation.php` registers ordered sections. `PmaDocumentationService` loads/parses frontmatter and converts Markdown via `league/commonmark`. `DocumentationController` serves index + section pages behind `super_admin`. Blade views provide a modern docs chrome inside `user/layouts/master`.

**Tech Stack:** Laravel 13, PHP 8.3+, `league/commonmark`, Blade, PHPUnit, Cursor `.mdc` rules

## Global Constraints

- Documentation menu visible only to Super Admin (`hasNewRole('SUPER ADMIN')` + `super_admin` middleware)
- Menu placed after the last existing sidebar item (after Chatbot Assistant block)
- Content storage: Markdown under `docs/pma/` only (no in-panel editor)
- Hybrid UX: overview cards on index; detail + accordions on section pages
- v1: index ready; all section stubs `coming_soon` (full prose deferred)
- Align UI with existing PMA colors; avoid purple-gradient template look
- Document only real product behavior; do not invent rules
- Do not commit unless the user explicitly asks

## File map

| File | Role |
|------|------|
| `composer.json` | Add direct `league/commonmark` dependency |
| `config/pma_documentation.php` | Ordered section registry |
| `docs/pma/index.md` | Overview markdown |
| `docs/pma/{slug}.md` | One stub per sidebar area |
| `app/Services/PmaDocumentationService.php` | Registry helpers, load MD, frontmatter, HTML |
| `app/Http/Controllers/User/DocumentationController.php` | `index`, `show` |
| `routes/web.php` | Documentation routes + `super_admin` |
| `resources/views/user/documentation/index.blade.php` | Cards + search |
| `resources/views/user/documentation/show.blade.php` | Section detail + mini-nav |
| `resources/views/user/documentation/partials/nav.blade.php` | Sticky section list |
| `public/user_assets/css/documentation.css` | Modern docs styles |
| `resources/views/user/includes/sidebar.blade.php` | Last Documentation link |
| `.cursor/rules/pma-documentation.mdc` | Always-apply agent rule |
| `tests/Unit/PmaDocumentationServiceTest.php` | Unit tests for service |
| `tests/Feature/DocumentationAccessTest.php` | Access / 403 / 404 smoke tests |

---

### Task 1: Composer + config registry + markdown stubs

**Files:**
- Modify: `composer.json` (add `"league/commonmark": "^2.8"` under `require`)
- Create: `config/pma_documentation.php`
- Create: `docs/pma/index.md`
- Create: `docs/pma/*.md` stubs for every registry section

**Interfaces:**
- Produces: `config('pma_documentation.sections')` — array of:
  - `slug` (string), `title` (string), `summary` (string), `icon` (string Font Awesome / image path key), `file` (string relative to `docs/pma/`), `status` (`ready`|`coming_soon`), `sidebar_key` (string)
- Produces: markdown files with frontmatter keys `title`, `updated`, `status`, `sidebar_key`

- [ ] **Step 1: Add CommonMark as a direct dependency**

Run:

```bash
cd /Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring
composer require league/commonmark:^2.8 --no-interaction
```

Expected: `composer.json` / `composer.lock` updated; package install succeeds.

- [ ] **Step 2: Create `config/pma_documentation.php`**

```php
<?php

return [
    'base_path' => base_path('docs/pma'),

    'sections' => [
        [
            'slug' => 'messaging',
            'title' => 'Messaging',
            'summary' => 'Chats, Team, and Mail.',
            'icon' => 'ti-email',
            'file' => 'messaging.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'messaging',
        ],
        [
            'slug' => 'education',
            'title' => 'Education',
            'summary' => 'Topics, becoming tracks, and education files.',
            'icon' => 'ti-book',
            'file' => 'education.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'education',
        ],
        [
            'slug' => 'bulletins',
            'title' => 'Bulletins',
            'summary' => 'Bulletin board, jobs, meetings, events, collaboration.',
            'icon' => 'ti-clipboard',
            'file' => 'bulletins.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'bulletins',
        ],
        [
            'slug' => 'e-store',
            'title' => 'E-Store',
            'summary' => 'Products, orders, warehouses, promo codes, store CMS.',
            'icon' => 'ti-shopping-cart',
            'file' => 'e-store.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'e_store',
        ],
        [
            'slug' => 'warehouse-store',
            'title' => 'Warehouse Store',
            'summary' => 'Warehouse-scoped products and orders.',
            'icon' => 'ti-package',
            'file' => 'warehouse-store.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'warehouse_store',
        ],
        [
            'slug' => 'e-learning',
            'title' => 'E-Learning',
            'summary' => 'E-learning catalog and CMS.',
            'icon' => 'ti-light-bulb',
            'file' => 'e-learning.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'e_learning',
        ],
        [
            'slug' => 'role-permission',
            'title' => 'Role Permission',
            'summary' => 'Roles and Spatie permissions.',
            'icon' => 'ti-lock',
            'file' => 'role-permission.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'role_permission',
        ],
        [
            'slug' => 'membership-management',
            'title' => 'Membership Management',
            'summary' => 'Plans, members, payments, promo codes, settings.',
            'icon' => 'ti-id-badge',
            'file' => 'membership-management.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'membership_management',
        ],
        [
            'slug' => 'all-members',
            'title' => 'All Members',
            'summary' => 'Partners / all members list.',
            'icon' => 'ti-user',
            'file' => 'all-members.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'all_members',
        ],
        [
            'slug' => 'user-activity',
            'title' => 'User Activity',
            'summary' => 'Activity dashboard and activity list.',
            'icon' => 'ti-bar-chart',
            'file' => 'user-activity.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'user_activity',
        ],
        [
            'slug' => 'signup-rules',
            'title' => 'Signup Rules',
            'summary' => 'Registration and signup rule configuration.',
            'icon' => 'ti-check-box',
            'file' => 'signup-rules.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'signup_rules',
        ],
        [
            'slug' => 'strategy',
            'title' => 'Strategy',
            'summary' => 'Strategy documents and management.',
            'icon' => 'ti-flag-alt',
            'file' => 'strategy.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'strategy',
        ],
        [
            'slug' => 'policy-guidance',
            'title' => 'Policy & Guidance',
            'summary' => 'Policy and guidance documents.',
            'icon' => 'ti-files',
            'file' => 'policy-guidance.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'policy_guidance',
        ],
        [
            'slug' => 'membership-self-service',
            'title' => 'Membership (Self-Service)',
            'summary' => 'Member-facing membership panel.',
            'icon' => 'ti-credit-card',
            'file' => 'membership-self-service.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'membership',
        ],
        [
            'slug' => 'restore',
            'title' => 'Restore',
            'summary' => 'Recycle bin — Super Admin only.',
            'icon' => 'ti-trash',
            'file' => 'restore.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'recycle_bin',
        ],
        [
            'slug' => 'donations',
            'title' => 'Donations',
            'summary' => 'Admin portal donations management.',
            'icon' => 'ti-heart',
            'file' => 'donations.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'donations',
        ],
        [
            'slug' => 'newsletters',
            'title' => 'Newsletters',
            'summary' => 'Newsletter CMS.',
            'icon' => 'ti-email',
            'file' => 'newsletters.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'newsletters',
        ],
        [
            'slug' => 'testimonials',
            'title' => 'Testimonials',
            'summary' => 'Testimonials list and create.',
            'icon' => 'ti-quote-left',
            'file' => 'testimonials.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'testimonials',
        ],
        [
            'slug' => 'our-governance',
            'title' => 'Our Governance',
            'summary' => 'Governance CMS entries.',
            'icon' => 'ti-briefcase',
            'file' => 'our-governance.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'our_governance',
        ],
        [
            'slug' => 'our-organizations',
            'title' => 'Our Organizations',
            'summary' => 'Organizations CMS entries.',
            'icon' => 'ti-world',
            'file' => 'our-organizations.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'our_organizations',
        ],
        [
            'slug' => 'organization-center',
            'title' => 'Organization Center',
            'summary' => 'Organization center CMS.',
            'icon' => 'ti-home',
            'file' => 'organization-center.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'organization_center',
        ],
        [
            'slug' => 'services',
            'title' => 'Services',
            'summary' => 'Per-organization services CMS.',
            'icon' => 'ti-settings',
            'file' => 'services.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'services',
        ],
        [
            'slug' => 'pages-cms',
            'title' => 'Pages (CMS)',
            'summary' => 'Home, About, FAQs, Gallery, legal pages, footer, agreements.',
            'icon' => 'ti-layout',
            'file' => 'pages-cms.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'pages',
        ],
        [
            'slug' => 'countries',
            'title' => 'Countries',
            'summary' => 'Admin countries management.',
            'icon' => 'ti-map-alt',
            'file' => 'countries.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'countries',
        ],
        [
            'slug' => 'site-settings',
            'title' => 'Site Settings',
            'summary' => 'Settings and menu names.',
            'icon' => 'ti-panel',
            'file' => 'site-settings.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'site_settings',
        ],
        [
            'slug' => 'super-admin',
            'title' => 'Super Admin',
            'summary' => 'Super admin user list / management.',
            'icon' => 'ti-shield',
            'file' => 'super-admin.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'admin',
        ],
        [
            'slug' => 'chatbot-assistant',
            'title' => 'Chatbot Assistant',
            'summary' => 'Chatbot dashboard, keywords, and history.',
            'icon' => 'ti-comments',
            'file' => 'chatbot-assistant.md',
            'status' => 'coming_soon',
            'sidebar_key' => 'chatbot',
        ],
    ],
];
```

- [ ] **Step 3: Create `docs/pma/index.md`**

```markdown
---
title: PMA Project Documentation
updated: 2026-07-24
status: ready
sidebar_key: documentation
---

# PMA Project Documentation

Internal Super Admin reference for the PMA user panel.

## How to use

1. Open a section card below (or in the left nav on a section page).
2. Read the **Overview**, then expand feature blocks for rules and conditions.
3. When product behavior changes, update the matching file under `docs/pma/` and bump `updated`.

## Coverage

Sections mirror the sidebar from **Messaging** through **Chatbot Assistant**. Stub sections show **Coming soon** until filled.
```

- [ ] **Step 4: Create one stub markdown file per section**

For each entry in `config/pma_documentation.php` `sections`, create `docs/pma/{file}` with:

```markdown
---
title: {title}
updated: 2026-07-24
status: coming_soon
sidebar_key: {sidebar_key}
---

# {title}

## Overview

Documentation for this area is coming soon. Until then, treat this page as a placeholder for features, permissions, rules, and conditions under **{title}**.

## Features

### Placeholder

- Full page-by-page rules will be added when this area is next changed or intentionally documented.
```

Replace `{title}`, `{sidebar_key}` from the config row. Filename must match `file` in config.

- [ ] **Step 5: Verify stubs exist**

Run:

```bash
ls docs/pma | wc -l
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo count(config('pma_documentation.sections'));"
```

Expected: markdown file count = `1` (index) + section count; config section count prints (27).

---

### Task 2: `PmaDocumentationService` + unit tests

**Files:**
- Create: `app/Services/PmaDocumentationService.php`
- Create: `tests/Unit/PmaDocumentationServiceTest.php`

**Interfaces:**
- Produces:
  - `PmaDocumentationService::sections(): array` — config sections list
  - `PmaDocumentationService::findSection(string $slug): ?array` — one section or null
  - `PmaDocumentationService::loadDocument(string $relativeFile): array` — returns `['meta' => array{title?:string,updated?:string,status?:string,sidebar_key?:string}, 'html' => string, 'body_markdown' => string]`
  - `PmaDocumentationService::renderMarkdown(string $markdown): string`
  - `PmaDocumentationService::parseFrontMatter(string $raw): array{0: array, 1: string}` — `[meta, body]`

- [ ] **Step 1: Write the failing unit test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PmaDocumentationServiceTest`

Expected: FAIL (class `PmaDocumentationService` not found)

- [ ] **Step 3: Implement `PmaDocumentationService`**

```php
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
        $path = $base . DIRECTORY_SEPARATOR . ltrim($relativeFile, DIRECTORY_SEPARATOR);

        $realBase = realpath($base);
        $realPath = realpath($path);

        if ($realBase === false || $realPath === false || !str_starts_with($realPath, $realBase) || !File::isFile($realPath)) {
            throw new RuntimeException('Documentation file not found: ' . $relativeFile);
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
        if (!preg_match('/\A---\s*\R(.*?)\R---\s*\R?(.*)\z/s', $raw, $matches)) {
            return [[], $raw];
        }

        $meta = [];
        foreach (preg_split('/\R/', trim($matches[1])) as $line) {
            if ($line === '' || !str_contains($line, ':')) {
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
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=PmaDocumentationServiceTest`

Expected: PASS (all 4 tests)

---

### Task 3: Controller, routes, access feature tests

**Files:**
- Create: `app/Http/Controllers/User/DocumentationController.php`
- Modify: `routes/web.php` (add use import + route group near recycle-bin)
- Create: `tests/Feature/DocumentationAccessTest.php`

**Interfaces:**
- Consumes: `PmaDocumentationService::sections()`, `findSection()`, `loadDocument()`
- Produces routes:
  - `GET /user/documentation` → `user.documentation.index`
  - `GET /user/documentation/{section}` → `user.documentation.show` (`section` regex: `[a-z0-9\-]+`)

- [ ] **Step 1: Write feature tests (failing)**

```php
<?php

namespace Tests\Feature;

use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class DocumentationAccessTest extends TestCase
{
    public function test_super_admin_middleware_blocks_non_super_admin(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };

        $this->actingAs($user);

        $middleware = new SuperAdminMiddleware();
        $request = Request::create('/user/documentation', 'GET');

        try {
            $middleware->handle($request, fn () => response('ok', 200));
            $this->fail('Expected 403 abort');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_super_admin_middleware_allows_super_admin(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return $roles === 'SUPER ADMIN' || (is_array($roles) && in_array('SUPER ADMIN', $roles, true));
            }
        };

        $this->actingAs($user);

        $middleware = new SuperAdminMiddleware();
        $request = Request::create('/user/documentation', 'GET');
        $response = $middleware->handle($request, fn () => response('ok', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }

    public function test_documentation_routes_are_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('user.documentation.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('user.documentation.show'));
    }
}
```

- [ ] **Step 2: Run filter — expect route test fail until routes exist**

Run: `php artisan test --filter=DocumentationAccessTest`

Expected: FAIL on missing routes (or class) until Steps 3–4 land.

- [ ] **Step 3: Create `DocumentationController`**

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PmaDocumentationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly PmaDocumentationService $documentation
    ) {
    }

    public function index(): View
    {
        $overview = $this->documentation->loadDocument('index.md');
        $sections = $this->documentation->sections();

        return view('user.documentation.index', [
            'overviewHtml' => $overview['html'],
            'overviewMeta' => $overview['meta'],
            'sections' => $sections,
        ]);
    }

    public function show(string $section): View
    {
        $configSection = $this->documentation->findSection($section);
        if ($configSection === null) {
            throw new NotFoundHttpException('Documentation section not found.');
        }

        $document = $this->documentation->loadDocument($configSection['file']);
        $status = $document['meta']['status'] ?? $configSection['status'] ?? 'coming_soon';

        return view('user.documentation.show', [
            'section' => $configSection,
            'sections' => $this->documentation->sections(),
            'meta' => $document['meta'],
            'html' => $document['html'],
            'status' => $status,
        ]);
    }
}
```

- [ ] **Step 4: Register routes in `routes/web.php`**

Add import near other User controller imports:

```php
use App\Http\Controllers\User\DocumentationController;
```

Inside the main `Route::prefix('user')->middleware([...])->group(...)`, immediately after the recycle-bin group (~line 621), add:

```php
    // Project Documentation (SUPER ADMIN Only)
    Route::prefix('documentation')->middleware('super_admin')->name('user.documentation.')->group(function () {
        Route::get('/', [DocumentationController::class, 'index'])->name('index');
        Route::get('/{section}', [DocumentationController::class, 'show'])
            ->where('section', '[a-z0-9\-]+')
            ->name('show');
    });
```

- [ ] **Step 5: Re-run feature tests**

Run: `php artisan test --filter=DocumentationAccessTest`

Expected: PASS

---

### Task 4: Modern Blade UI + CSS

**Files:**
- Create: `public/user_assets/css/documentation.css`
- Create: `resources/views/user/documentation/partials/nav.blade.php`
- Create: `resources/views/user/documentation/index.blade.php`
- Create: `resources/views/user/documentation/show.blade.php`

**Interfaces:**
- Consumes view data from Task 3 (`sections`, `overviewHtml`, `section`, `html`, `meta`, `status`)
- Index search: client-side filter on `[data-doc-card]` via `data-title` / `data-summary`
- Section HTML: wrap rendered markdown in `.pma-doc-content`; JS turns consecutive `h2`/`h3`+following siblings into accordion items (or use `<details>` for each `h2` block)

- [ ] **Step 1: Add `documentation.css`**

Create a self-contained stylesheet using CSS variables aligned to PMA (dark navy / warm gold accents already used in panel — inspect `style.css` for `--` or primary hex and reuse). Include:

- `.pma-docs` page shell
- `.pma-docs-hero` title + intro
- `.pma-docs-search` input
- `.pma-docs-grid` responsive card grid
- `.pma-docs-card` with Ready / Coming soon badge
- `.pma-docs-layout` (sidebar nav + main)
- `.pma-docs-nav` sticky left nav
- `.pma-docs-content` typography for markdown HTML
- `.pma-docs-accordion` / `.pma-docs-accordion-item` expand/collapse
- Soft gradients / subtle pattern background (not flat single color; not purple AI default)

- [ ] **Step 2: Create nav partial**

`resources/views/user/documentation/partials/nav.blade.php`:

```blade
<nav class="pma-docs-nav" aria-label="Documentation sections">
    <a href="{{ route('user.documentation.index') }}"
       class="pma-docs-nav-link {{ request()->routeIs('user.documentation.index') ? 'is-active' : '' }}">
        Overview
    </a>
    @foreach ($sections as $navSection)
        <a href="{{ route('user.documentation.show', $navSection['slug']) }}"
           class="pma-docs-nav-link {{ isset($section) && ($section['slug'] ?? '') === $navSection['slug'] ? 'is-active' : '' }}">
            <span>{{ $navSection['title'] }}</span>
            @if (($navSection['status'] ?? '') === 'coming_soon')
                <span class="pma-docs-badge pma-docs-badge--soon">Soon</span>
            @endif
        </a>
    @endforeach
</nav>
```

- [ ] **Step 3: Create index view**

```blade
@extends('user.layouts.master')

@section('title', 'Documentation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pma-docs">
    <header class="pma-docs-hero">
        <p class="pma-docs-eyebrow">Super Admin</p>
        <h1>PMA Project Documentation</h1>
        <p class="pma-docs-lead">Features, rules, and conditions for every area from Messaging through the end of the sidebar.</p>
        <label class="pma-docs-search">
            <span class="visually-hidden">Search sections</span>
            <input type="search" id="pma-docs-search" placeholder="Search sections…" autocomplete="off">
        </label>
    </header>

    <div class="pma-docs-overview">
        {!! $overviewHtml !!}
    </div>

    <div class="pma-docs-grid" id="pma-docs-grid">
        @foreach ($sections as $card)
            <a class="pma-docs-card"
               href="{{ route('user.documentation.show', $card['slug']) }}"
               data-doc-card
               data-title="{{ strtolower($card['title']) }}"
               data-summary="{{ strtolower($card['summary']) }}">
                <div class="pma-docs-card-top">
                    <span class="pma-docs-card-icon"><i class="{{ $card['icon'] }}"></i></span>
                    @if (($card['status'] ?? '') === 'ready')
                        <span class="pma-docs-badge pma-docs-badge--ready">Ready</span>
                    @else
                        <span class="pma-docs-badge pma-docs-badge--soon">Coming soon</span>
                    @endif
                </div>
                <h2>{{ $card['title'] }}</h2>
                <p>{{ $card['summary'] }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('pma-docs-search');
    const cards = document.querySelectorAll('[data-doc-card]');
    if (!input) return;
    input.addEventListener('input', function () {
        const q = (input.value || '').toLowerCase().trim();
        cards.forEach(function (card) {
            const hay = (card.getAttribute('data-title') || '') + ' ' + (card.getAttribute('data-summary') || '');
            card.hidden = q !== '' && hay.indexOf(q) === -1;
        });
    });
})();
</script>
@endpush
```

Note: if Themify `ti-*` icons are not loaded in master, use existing panel icon pattern (`<img src="{{ asset('user_assets/images/...') }}">`) or Bootstrap Icons already on the layout — match whatever master already includes; adjust `icon` values in config accordingly during implementation.

- [ ] **Step 4: Create show view**

```blade
@extends('user.layouts.master')

@section('title', ($section['title'] ?? 'Documentation'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pma-docs">
    <div class="pma-docs-layout">
        <aside class="pma-docs-aside">
            @include('user.documentation.partials.nav')
        </aside>
        <article class="pma-docs-main">
            <header class="pma-docs-section-header">
                <h1>{{ $meta['title'] ?? $section['title'] }}</h1>
                @if (!empty($meta['updated']))
                    <p class="pma-docs-updated">Last updated: {{ $meta['updated'] }}</p>
                @endif
                @if ($status === 'coming_soon')
                    <div class="pma-docs-placeholder" role="status">
                        Full documentation for this area is coming soon. The outline below will be replaced as features are documented.
                    </div>
                @endif
            </header>
            <div class="pma-doc-content" id="pma-doc-content">
                {!! $html !!}
            </div>
        </article>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('pma-doc-content');
    if (!root) return;
    const headings = root.querySelectorAll('h2');
    if (!headings.length) return;

    headings.forEach(function (h2) {
        const wrap = document.createElement('div');
        wrap.className = 'pma-docs-accordion-item';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pma-docs-accordion-trigger';
        btn.setAttribute('aria-expanded', 'false');
        btn.textContent = h2.textContent;
        const panel = document.createElement('div');
        panel.className = 'pma-docs-accordion-panel';
        panel.hidden = true;

        let node = h2.nextSibling;
        while (node && !(node.nodeType === 1 && node.tagName === 'H2')) {
            const next = node.nextSibling;
            panel.appendChild(node);
            node = next;
        }
        h2.replaceWith(wrap);
        wrap.appendChild(btn);
        wrap.appendChild(panel);
        btn.addEventListener('click', function () {
            const open = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', open ? 'false' : 'true');
            panel.hidden = open;
        });
    });
})();
</script>
@endpush
```

- [ ] **Step 5: Manual UI check**

Log in as Super Admin, open `/user/documentation`, confirm hero + cards + search; open a stub section; confirm accordion + left nav. Adjust CSS if icons/fonts clash with master.

---

### Task 5: Sidebar link (last menu) + Cursor rule

**Files:**
- Modify: `resources/views/user/includes/sidebar.blade.php` (after Chatbot `@endif`, before the trailing `<br>`s / `</ul>`)
- Create: `.cursor/rules/pma-documentation.mdc`

**Interfaces:**
- Sidebar only when `Auth::user()->hasNewRole('SUPER ADMIN')`
- Active state: `Request::is('user/documentation*')`
- Route: `user.documentation.index`

- [ ] **Step 1: Insert Documentation sidebar item after Chatbot block**

Locate the Chatbot Assistant `@endif` near the end of the `<ul>` (after ~line 1292). Immediately after that `@endif` and before the spacer `<br>` tags, insert:

```blade
                 {{-- Project Documentation - SUPER ADMIN Only --}}
                 @if (Auth::check() && Auth::user()->hasNewRole('SUPER ADMIN'))
                     <li class="sidebar-item">
                         <a class="sidebar-link {{ Request::is('user/documentation*') ? 'active' : '' }}"
                             href="{{ route('user.documentation.index') }}" aria-expanded="false">
                             <span>
                                 <img src="{{ asset('user_assets/images/ICON/Help.svg') }}"
                                     alt="">
                             </span>
                             <span class="hide-menu">Documentation</span>
                         </a>
                     </li>
                 @endif
```

If `Help.svg` is missing, reuse another existing icon under `user_assets/images/` (e.g. strategy/policy icon). Do not invent a new binary asset unless needed.

- [ ] **Step 2: Create `.cursor/rules/pma-documentation.mdc`**

```markdown
---
description: Keep PMA Super Admin Documentation in sync when user-panel features change
alwaysApply: true
---

# PMA Documentation Sync

When starting any task in this repo:

1. Briefly state whether the work affects PMA user-panel documentation under `docs/pma/` (which section slug), or that no documentation update is needed.
2. If you change features, rules, conditions, permissions, or pages for any sidebar area from Messaging through Chatbot (including Admin Portal CMS), update the matching `docs/pma/*.md` file in the same change and bump frontmatter `updated`.
3. If you add a new PMA sidebar menu, add a row to `config/pma_documentation.php`, create a stub markdown file, and ensure the Documentation UI picks it up.
4. Only document behavior that exists in code/product — do not invent rules.
5. Documentation UI is Super Admin–only; do not expose it to other roles.
```

- [ ] **Step 3: Verify sidebar placement**

As Super Admin: Documentation appears after Chatbot (last item). As a normal user: item hidden. Direct `/user/documentation` as non–Super Admin → 403.

- [ ] **Step 4: Run full related tests**

Run:

```bash
php artisan test --filter='PmaDocumentationServiceTest|DocumentationAccessTest'
```

Expected: all PASS

---

## Spec coverage checklist

| Spec requirement | Task |
|------------------|------|
| Super Admin–only menu + middleware | 3, 5 |
| Last sidebar item | 5 |
| Markdown under `docs/pma/` | 1 |
| Config registry | 1 |
| Modern hybrid UI (cards + section detail) | 4 |
| Search on index | 4 |
| Coming soon placeholders | 1, 4 |
| CommonMark rendering + frontmatter | 2 |
| Cursor always-apply rule | 5 |
| Incremental content (stubs first) | 1 |
| No in-panel editor | (non-goal; no task) |

## Plan self-review

- No TBD/placeholder steps remaining
- Service method names consistent across Tasks 2–4
- Route names `user.documentation.index` / `user.documentation.show` consistent
- Commits omitted unless user asks (Global Constraints)
