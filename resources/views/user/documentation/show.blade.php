@extends('user.layouts.master')

@section('title', ($section['title'] ?? 'Documentation'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}?v={{ @filemtime(public_path('user_assets/css/documentation.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="bg_white_border pma-docs">
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

        <article class="card pma-docs-panel">
            <div class="card-body">
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

                <h3>{{ $meta['title'] ?? $section['title'] }}</h3>
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
                    <div class="row g-3 pma-docs-topic-list">
                        @foreach ($childSections as $child)
                            <div class="col-12 col-md-6">
                                <a class="card pma-docs-topic-card h-100" href="{{ route('user.documentation.show', $child['slug']) }}">
                                    <div class="card-body">
                                        <span class="pma-docs-topic-icon" aria-hidden="true"><i class="{{ $child['icon'] }}"></i></span>
                                        <span class="pma-docs-topic-text">
                                            <strong>{{ $child['title'] }}</strong>
                                            <span>{{ $child['summary'] }}</span>
                                        </span>
                                        <span class="pma-docs-hub-go">Open <span aria-hidden="true">→</span></span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
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
            </div>
        </article>
    </div>
</div>
@endsection
