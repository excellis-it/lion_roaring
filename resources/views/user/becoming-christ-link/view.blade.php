@extends('user.layouts.master')
@section('title')
Becoming Christ Like View - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css">
    <style>
        .fc-button-primary {
            background-color: #7851a9 !important;
            border-color: #7851a9 !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row mb-3">
                                <div class="col-md-10">
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h3 class="mb-3 float-left">Becoming Christ Like File</h3>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('becoming-christ-link.index') }}" class="btn btn-primary w-100">< Back</a>
                                </div>
                            </div>
                            <div >
                                @if ($file->file_extension == 'pdf')
                                <iframe id="myIframe" src="{{ Storage::url($file->file) }}#toolbar=0" width="100%" height="1000"></iframe>

                                @elseif ($file->file_extension == 'jpg' || $file->file_extension == 'jpeg' || $file->file_extension == 'png' || $file->file_extension == 'gif' || $file->file_extension == 'svg' || $file->file_extension == 'webp')
                                    <img src="{{ Storage::url($file->file) }}" alt="file" width="100%" height="600px">'
                                @elseif ($file->file_extension == 'mp4' || $file->file_extension == 'webm' || $file->file_extension == 'ogg')
                                    <video width="100%" height="600px" controls>
                                        <source src="{{ Storage::url($file->file) }}" type="video/mp4">
                                        <source src="{{ Storage::url($file->file) }}" type="video/webm">
                                        <source src="{{ Storage::url($file->file) }}" type="video/ogg">
                                    </video>
                                @elseif ($file->file_extension == 'mp3' || $file->file_extension == 'wav' || $file->file_extension == 'ogg')
                                    <audio width="100%" height="600px" controls>
                                        <source src="{{ Storage::url($file->file) }}" type="audio/mp3">
                                        <source src="{{ Storage::url($file->file) }}" type="audio/wav">
                                        <source src="{{ Storage::url($file->file) }}" type="audio/ogg">
                                    </audio>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- don't right click --}}
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
</script>
<script>
    // Get the iframe element by its ID
    const iframe = document.getElementById('myIframe');

    // Add an event listener to prevent the context menu from appearing on right-click
    iframe.addEventListener('contextmenu', (event) => {
        event.preventDefault();
    });
</script>
@endpush
