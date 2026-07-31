@extends('errors.layout')

@php
    // never surface the exception text here — it leaks internals
    try {
        $occurredAt = now()->format('d M Y, H:i:s T');
    } catch (\Throwable $e) {
        $occurredAt = gmdate('d M Y, H:i:s') . ' UTC';
    }
@endphp

@section('title', '500 — Something Went Wrong')
@section('heading', 'Something went wrong')
@section('message', 'We hit an unexpected problem while handling your request. Nothing you did caused this, and the issue
    has been recorded.')
@section('hint', 'Please try again in a moment. If it keeps happening, contact support and quote the time below.')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.3 3.9L1.9 18.4A2 2 0 0 0 3.6 21.4h16.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" />
        <path d="M12 9.5v4.2" />
        <circle cx="12" cy="17.2" r="1.05" fill="#643271" stroke="none" />
    </svg>
@endsection

@section('extra')
    <div class="ref">{{ $occurredAt }}</div>
@endsection
