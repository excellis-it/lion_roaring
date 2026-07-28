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
                        class="form-control notranslate @error('description') is-invalid @enderror"
                        translate="no">{{ old('description', $changeLog->description) }}</textarea>
                    <small class="text-muted">Use bold/italic and bullet or numbered lists only.</small>
                    @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Publish Date</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', $publishedAtLocal) }}">
                    <small class="text-muted">Past or current only — future dates are not allowed. Saving publishes immediately.</small>
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
    <style>
        #changeLogForm .note-editable ul { list-style-type: disc; padding-left: 1.5rem; margin: 0.35rem 0; }
        #changeLogForm .note-editable ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0.35rem 0; }
        #changeLogForm .note-editable li { margin-bottom: 0.25rem; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(function () {
            var $desc = $('#description');
            $desc.summernote({
                placeholder: 'Describe what changed…',
                tabsize: 2,
                height: 260,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                ],
                callbacks: {
                    onInit: function () {
                        var $editable = $desc.next('.note-editor').find('.note-editable');
                        $editable.addClass('notranslate').attr('translate', 'no');
                    }
                }
            });

            var $publishedAt = $('#published_at');
            function setPublishMax() {
                var now = new Date();
                var pad = function (n) { return String(n).padStart(2, '0'); };
                var max = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate())
                    + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
                $publishedAt.attr('max', max);
            }
            setPublishMax();
            setInterval(setPublishMax, 30000);

            $('#changeLogForm').on('submit', function (e) {
                var code = $desc.summernote('code');
                $desc.val(code);
                var text = $('<div>').html(code).text().replace(/\u00a0/g, ' ').trim();
                if (!text) {
                    e.preventDefault();
                    alert('The description field is required.');
                    $desc.summernote('focus');
                    return false;
                }
                setPublishMax();
                if ($publishedAt.val() && $publishedAt.attr('max') && $publishedAt.val() > $publishedAt.attr('max')) {
                    e.preventDefault();
                    alert('Publish date cannot be in the future.');
                    return false;
                }
            });
        });
    </script>
@endpush
