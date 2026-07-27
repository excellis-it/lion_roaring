@extends('user.layouts.master')
@section('title')
    Edit Change Log - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h3 class="mb-0">Edit Change Log Entry</h3>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('change-logs.index', ['platform' => $changeLog->platform]) }}" class="btn btn-primary">Cancel</a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('change-logs.update', $changeLog) }}" method="POST" id="changeLogForm">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Platform <span class="text-danger">*</span></label>
                    <select name="platform" class="form-control @error('platform') is-invalid @enderror" required>
                        <option value="web" @selected(old('platform', $changeLog->platform) === 'web')>Web Version</option>
                        <option value="mobile" @selected(old('platform', $changeLog->platform) === 'mobile')>Mobile App Version</option>
                    </select>
                    @error('platform')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Version <span class="text-danger">*</span></label>
                    <input type="text" name="version" class="form-control @error('version') is-invalid @enderror"
                        value="{{ old('version', $changeLog->version) }}" required>
                    @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror">
                        @foreach(['feature','improvement','bugfix','security'] as $type)
                            <option value="{{ $type }}" @selected(old('type', $changeLog->type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $changeLog->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="10"
                        class="form-control @error('description') is-invalid @enderror"
                        required>{{ old('description', $changeLog->description) }}</textarea>
                    <small class="text-muted">Use bold/italic and bullet or numbered lists only.</small>
                    @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', $changeLog->published_at?->format('Y-m-d\TH:i')) }}">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Entry</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#description').summernote({
            placeholder: 'Describe what changed…',
            tabsize: 2,
            height: 260,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
            ]
        });

        $('#changeLogForm').on('submit', function () {
            $('#description').val($('#description').summernote('code'));
        });
    </script>
@endpush
