@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Membership Settings
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.membership.settings') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="heading_box mb-4">
                            <h3>Measurement Settings</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Measurement Label</label>
                            <input type="text" class="form-control" name="label"
                                value="{{ $measurement->label ?? '' }}" placeholder="e.g., USD/year">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" hidden>
                        <div class="box_label">
                            <label>Yearly Dues</label>
                            <input name="yearly_dues" class="form-control" type="number" step="0.01"
                                value="{{ $measurement->yearly_dues ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3" hidden>
                        <div class="box_label">
                            <label>Measurement Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $measurement->description ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Membership Card Title</label>
                            <input type="text" class="form-control" name="membership_card_title"
                                value="{{ $measurement->membership_card_title ?? 'My Current Membership' }}"
                                placeholder="e.g. My Current Membership">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Renewal Reminder Days Before Expiry</label>
                            <input type="number" class="form-control" name="renewal_reminder_days" min="1"
                                max="365"
                                value="{{ old('renewal_reminder_days', $measurement->renewal_reminder_days ?? 7) }}"
                                placeholder="e.g. 7">
                            <small class="text-muted">Used by the daily Laravel scheduler. Example: set to 1 for quick
                                test.</small>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Renewal Reminder Email Subject</label>
                            <input type="text" class="form-control" name="renewal_reminder_subject"
                                value="{{ old('renewal_reminder_subject', $measurement->renewal_reminder_subject ?? 'Your subscription will expire soon') }}"
                                placeholder="e.g. @{{ name }}, your membership expires in @{{ days_remaining }} day(s)">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Renewal Reminder Email Body (HTML/Text)</label>
                            <textarea id="renewal_reminder_body" name="renewal_reminder_body" class="form-control"
                                placeholder="Use placeholders like @{{ name }}, @{{ subscription_name }}, @{{ expire_date }}, @{{ days_remaining }}, @{{ renew_url }}">{{ old('renewal_reminder_body', $measurement->renewal_reminder_body ?? '') }}</textarea>
                            <small class="text-muted d-block mt-1">
                                Placeholders: @{{ name }}, @{{ subscription_name }}, @{{ start_date }},
                                @{{ expire_date }}, @{{ days_remaining }}, @{{ renew_url }},
                                @{{ app_name }}.
                                Leave empty to use the default built-in template.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 text-end">
                            <button type="submit" class="print_btn">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#renewal_reminder_body').summernote({
            tabsize: 2,
            height: 220
        });

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
