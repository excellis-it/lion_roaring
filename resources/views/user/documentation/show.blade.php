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
