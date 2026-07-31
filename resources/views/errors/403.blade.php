@extends('errors.layout')

@php
    $detail = trim((string) (($exception ?? null)?->getMessage() ?? ''));
    // Laravel falls back to the status text when no custom message was passed
    if ($detail === '' || strtolower($detail) === 'forbidden') {
        $detail = 'You do not have permission to access this page.';
    }
@endphp

@section('title', '403 — Access Denied')
@section('heading', 'Access denied')
@section('message', $detail)
@section('hint', 'If you believe this is a mistake, contact your administrator to have the right permission assigned to
    your role.')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="10.5" width="16" height="10.5" rx="2.5" />
        <path d="M8 10.5V7a4 4 0 0 1 8 0v3.5" />
        <circle cx="12" cy="15.6" r="1.15" fill="#643271" stroke="none" />
        <path d="M12 16.8v1.6" />
    </svg>
@endsection
