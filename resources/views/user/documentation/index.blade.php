@extends('user.layouts.master')

@section('title', 'Project Documentation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pma-docs">
    <div class="pma-docs-shell">
        <header class="pma-docs-hero">
            <h1>Project Documentation</h1>
            <p class="pma-docs-lead">Pick one area below. You will see its rules, then optional deeper topics — with a clear Back button at every step.</p>
        </header>

        <div class="pma-docs-hub-list" role="list">
            @foreach ($hubs as $index => $card)
                <a class="pma-docs-hub-row"
                   role="listitem"
                   href="{{ route('user.documentation.show', $card['slug']) }}">
                    <span class="pma-docs-hub-num">{{ $index + 1 }}</span>
                    <span class="pma-docs-hub-icon" aria-hidden="true"><i class="{{ $card['icon'] }}"></i></span>
                    <span class="pma-docs-hub-body">
                        <span class="pma-docs-hub-title">{{ $card['title'] }}</span>
                        <span class="pma-docs-hub-desc">{{ $card['summary'] }}</span>
                    </span>
                    <span class="pma-docs-hub-go" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
