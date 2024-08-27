@extends('user.layouts.master')
@section('title')
Strategy View - {{ env('APP_NAME') }}
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
                                    <h3 class="mb-3 float-left">Strategy File</h3>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('strategy.index') }}" class="btn btn-primary w-100">< Back</a>
                                </div>
                            </div>
                            <div >
                                @if ($strategy->file_extension == 'pdf')
                                <iframe id="myIframe" src="{{ Storage::url($strategy->file) }}#toolbar=0" width="100%" height="1000"></iframe>

                                @elseif ($strategy->file_extension == 'jpg' || $strategy->file_extension == 'jpeg' || $strategy->file_extension == 'png' || $strategy->file_extension == 'gif' || $strategy->file_extension == 'svg' || $strategy->file_extension == 'webp')
                                    <img src="{{ Storage::url($strategy->file) }}" alt="file" width="100%" height="600px">'
                                @elseif ($strategy->file_extension == 'mp4' || $strategy->file_extension == 'webm' || $strategy->file_extension == 'ogg')
                                    <video width="100%" height="600px" controls>
                                        <source src="{{ Storage::url($strategy->file) }}" type="video/mp4">
                                        <source src="{{ Storage::url($strategy->file) }}" type="video/webm">
                                        <source src="{{ Storage::url($strategy->file) }}" type="video/ogg">
                                    </video>
                                @elseif ($strategy->file_extension == 'mp3' || $strategy->file_extension == 'wav' || $strategy->file_extension == 'ogg')
                                    <audio width="100%" height="600px" controls>
                                        <source src="{{ Storage::url($strategy->file) }}" type="audio/mp3">
                                        <source src="{{ Storage::url($strategy->file) }}" type="audio/wav">
                                        <source src="{{ Storage::url($strategy->file) }}" type="audio/ogg">
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
