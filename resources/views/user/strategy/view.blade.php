@extends('user.layouts.master')
@section('title')
Strategy View - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3 float-left">Strategy File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('strategy.index') }}" class="btn btn-primary w-100">< Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($strategy->file_extension == 'pdf')
                                        @include('user.includes.pdf-document-viewer', ['pdfUrl' => Storage::url($strategy->file)])
                                    @elseif ($strategy->file_extension == 'jpg' || $strategy->file_extension == 'jpeg' || $strategy->file_extension == 'png' || $strategy->file_extension == 'gif' || $strategy->file_extension == 'svg' || $strategy->file_extension == 'webp')
                                        <img src="{{ Storage::url($strategy->file) }}" alt="file" width="100%" height="600px">
                                    @elseif ($strategy->file_extension == 'mp4' || $strategy->file_extension == 'webm')
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($strategy->file) }}" type="video/mp4">
                                        </video>
                                    @elseif ($strategy->file_extension == 'mp3' || $strategy->file_extension == 'wav')
                                        <audio width="100%" controls>
                                            <source src="{{ Storage::url($strategy->file) }}" type="audio/mp3">
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
