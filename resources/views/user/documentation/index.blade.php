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
