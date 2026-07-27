@extends('user.layouts.master')
@section('title')
    Submit Support Report - {{ env('APP_NAME') }}
@endsection
@section('content')
<style>
    .sr-form-wrap { max-width: 760px; }
    .sr-form-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .sr-form-header h3 { margin: 0; font-weight: 700; }
    .sr-form-header p { margin: 0.35rem 0 0; color: #6b7280; font-size: 0.9rem; }
    .sr-form-card {
        border: 1px solid #ece8f4; border-radius: 14px; padding: 1.25rem 1.35rem;
        background: linear-gradient(180deg, #fff 0%, #fcfbfe 100%);
    }
</style>
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="sr-form-header">
            <div>
                <h3>Submit Support Report</h3>
                <p>Describe the issue clearly. Our team will follow up and you can track status here.</p>
            </div>
            <a href="{{ route('support-reports.index') }}" class="btn btn-primary">Cancel</a>
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

        <div class="sr-form-wrap">
            <div class="sr-form-card">
                <form action="{{ route('support-reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror"
                            value="{{ old('subject') }}" placeholder="Briefly describe your issue" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" rows="7"
                            class="form-control @error('message') is-invalid @enderror"
                            placeholder="Describe your issue in detail" required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label fw-semibold">Attachment <span class="text-muted fw-normal">(optional, max 5MB — jpg, png, pdf, doc, docx)</span></label>
                        <input type="file" name="attachment" id="attachment"
                            class="form-control @error('attachment') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
