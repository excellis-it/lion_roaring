@extends('user.layouts.master')
@section('title')
Becoming a Leader View - {{ env('APP_NAME') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-10"></div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <h3 class="mb-3 float-left">Becoming a Leader File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('leadership-development.index') . '?topic=' . ($new_topic ?? '') }}" class="btn btn-primary w-100">< Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($file->file_extension == 'pdf')
                                        @include('user.includes.pdf-document-viewer', ['pdfUrl' => Storage::url($file->file)])
                                    @elseif ($file->file_extension == 'jpg' || $file->file_extension == 'jpeg' || $file->file_extension == 'png' || $file->file_extension == 'gif' || $file->file_extension == 'svg' || $file->file_extension == 'webp')
                                        <img src="{{ Storage::url($file->file) }}" alt="file" width="100%" height="600px">
                                    @elseif ($file->file_extension == 'mp4' || $file->file_extension == 'webm')
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($file->file) }}" type="video/mp4">
                                        </video>
                                    @elseif ($file->file_extension == 'mp3' || $file->file_extension == 'wav')
                                        <audio width="100%" controls>
                                            <source src="{{ Storage::url($file->file) }}" type="audio/mp3">
                                        </audio>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
</script>
@endpush
