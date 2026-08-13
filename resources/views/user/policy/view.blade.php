@extends('user.layouts.master')
@section('title')
Policy and Guidance View - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3 float-left">Policy and Guidance File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('policy-guidence.index') }}" class="btn btn-primary w-100">< Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($policy->file_extension == 'pdf')
                                        @include('user.includes.pdf-document-viewer', ['pdfUrl' => Storage::url($policy->file)])
                                    @elseif ($policy->file_extension == 'jpg' || $policy->file_extension == 'jpeg' || $policy->file_extension == 'png' || $policy->file_extension == 'gif' || $policy->file_extension == 'svg' || $policy->file_extension == 'webp')
                                        <img src="{{ Storage::url($policy->file) }}" alt="file" width="100%" height="600px">
                                    @elseif ($policy->file_extension == 'mp4' || $policy->file_extension == 'webm')
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($policy->file) }}" type="video/mp4">
                                        </video>
                                    @elseif ($policy->file_extension == 'mp3' || $policy->file_extension == 'wav')
                                        <audio width="100%" controls>
                                            <source src="{{ Storage::url($policy->file) }}" type="audio/mp3">
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
