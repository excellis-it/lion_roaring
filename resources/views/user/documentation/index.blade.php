@extends('user.layouts.master')

@section('title', 'Project Documentation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('user_assets/css/documentation.css') }}?v={{ @filemtime(public_path('user_assets/css/documentation.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="bg_white_border pma-docs">
        <header class="pma-docs-hero">
            <h3>Project Documentation</h3>
            <div class="pma-docs-lead">Pick one area below. You will see its rules, then optional deeper topics — with a clear Back button at every step.</div>
        </header>

        <div class="row g-4" role="list">
            @foreach ($hubs as $index => $card)
                <div class="col-12 col-md-6 col-xl-4" role="listitem">
                    <a class="card pma-docs-hub-card h-100"
                       href="{{ route('user.documentation.show', $card['slug']) }}">
                        <div class="card-body">
                            <span class="pma-docs-hub-card-top">
                                <span class="pma-docs-hub-num">{{ $index + 1 }}</span>
                                <span class="pma-docs-hub-icon" aria-hidden="true"><i class="{{ $card['icon'] }}"></i></span>
                            </span>
                            <span class="pma-docs-hub-title">{{ $card['title'] }}</span>
                            <span class="pma-docs-hub-desc">{{ $card['summary'] }}</span>
                            <span class="pma-docs-hub-go">Open <span aria-hidden="true">→</span></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
