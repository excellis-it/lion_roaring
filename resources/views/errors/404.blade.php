@extends('errors.layout')

@section('title', '404 — Page Not Found')
@section('heading', 'Page not found')
@section('message', 'The page you are looking for does not exist, or it may have been moved or removed.')
@section('hint', 'Check the address for typos, or use the links below to get back on track.')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="10.5" cy="10.5" r="6.5" />
        <path d="M15.4 15.4L21 21" />
        <path d="M8.2 8.2l4.6 4.6M12.8 8.2l-4.6 4.6" />
    </svg>
@endsection
