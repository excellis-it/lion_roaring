@extends('user.layouts.master')

@section('title', ($section['title'] ?? 'Documentation'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pma-docs">
    <div class="pma-docs-shell">
        <div class="pma-docs-toolbar">
            <a class="pma-docs-back" href="{{ $backUrl }}">
                <span class="pma-docs-back-icon" aria-hidden="true">←</span>
                <span>Back to {{ $backLabel }}</span>
            </a>
            <p class="pma-docs-step">
                @if (!empty($isHub))
                    Step 2 of 3 · Surface guide
                @else
                    Step 3 of 3 · Topic detail
                @endif
            </p>
        </div>

        <article class="pma-docs-panel">
            <header class="pma-docs-panel-head">
                @if (!empty($parentHub))
                    <p class="pma-docs-crumb">
                        <a href="{{ route('user.documentation.index') }}">Documentation</a>
                        <span aria-hidden="true">/</span>
                        <a href="{{ route('user.documentation.show', $parentHub['slug']) }}">{{ $parentHub['title'] }}</a>
                        <span aria-hidden="true">/</span>
                        <span>{{ $meta['title'] ?? $section['title'] }}</span>
                    </p>
                @else
                    <p class="pma-docs-crumb">
                        <a href="{{ route('user.documentation.index') }}">Documentation</a>
                        <span aria-hidden="true">/</span>
                        <span>{{ $meta['title'] ?? $section['title'] }}</span>
                    </p>
                @endif

                <h1>{{ $meta['title'] ?? $section['title'] }}</h1>
                @if (!empty($meta['updated']))
                    <p class="pma-docs-updated">Updated {{ $meta['updated'] }}</p>
                @endif
            </header>

            <div class="pma-docs-body">
                {!! $html !!}
            </div>

            @if (!empty($isHub) && !empty($childSections))
                <section class="pma-docs-next" aria-labelledby="pma-docs-next-title">
                    <h2 id="pma-docs-next-title">Next · Browse topics in this area</h2>
                    <p class="pma-docs-next-lead">Optional deeper pages. Open one, then use Back to return here.</p>
                    <ul class="pma-docs-topic-list">
                        @foreach ($childSections as $child)
                            <li>
                                <a href="{{ route('user.documentation.show', $child['slug']) }}">
                                    <span class="pma-docs-topic-icon" aria-hidden="true"><i class="{{ $child['icon'] }}"></i></span>
                                    <span class="pma-docs-topic-text">
                                        <strong>{{ $child['title'] }}</strong>
                                        <span>{{ $child['summary'] }}</span>
                                    </span>
                                    <span class="pma-docs-hub-go" aria-hidden="true">→</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <div class="pma-docs-footer-nav">
                <a class="pma-docs-back pma-docs-back--solid" href="{{ $backUrl }}">
                    <span class="pma-docs-back-icon" aria-hidden="true">←</span>
                    <span>Back to {{ $backLabel }}</span>
                </a>
                @if (empty($isHub))
                    <a class="pma-docs-home-link" href="{{ route('user.documentation.index') }}">All documentation</a>
                @endif
            </div>
        </article>
    </div>
</div>
@endsection
