@extends('user.layouts.master')
@section('title')
    File View - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3 float-left">File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('file.index') }}" class="btn btn-primary w-100">< Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($file->file_extension == 'pdf')
                                        @include('user.includes.pdf-document-viewer', ['pdfUrl' => Storage::url($file->file)])
                                    @elseif (in_array($file->file_extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                        <img src="{{ Storage::url($file->file) }}" alt="file"
                                            style="width:100%; height:auto; max-height:80vh; background:#fff;">
                                    @elseif (in_array($file->file_extension, ['mp4', 'webm']))
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($file->file) }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @elseif (in_array($file->file_extension, ['mp3', 'wav']))
                                        <audio controls style="width:100%;">
                                            <source src="{{ Storage::url($file->file) }}">
                                            Your browser does not support the audio element.
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
