@extends('user.layouts.master')
@section('title')
    Dashboard - {{ env('APP_NAME') }} user {{ $name }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
        <h3>
            The {{ $name }} page is under construction.
        </h3>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
