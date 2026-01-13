@extends('user.layouts.master')
@section('title')
    Save Email Template - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-md-12">
                    <!-- Placeholders card (same markup) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-2">Available placeholders</h5>
                            <p class="text-muted small mb-2">Click to insert into the editor. Copy icon copies the token.</p>

                            <div id="placeholders" class="d-flex flex-wrap gap-2">
                                @php
                                    $placeholders = [
                                        '{customer_name}',
                                        '{customer_email}',
                                        '{order_list}',
                                        '{order_id}',
                                        '{arriving_date}',
                                        '{total_order_value}',
                                        '{order_details_url_button}',
                                    ];
                                @endphp

                                @foreach ($placeholders as $ph)
                                    <div class="placeholder-chip btn btn-outline-secondary btn-sm d-inline-flex align-items-center"
                                        data-token="{{ $ph }}" title="Insert {{ $ph }}">
                                        <code class="me-2">{{ $ph }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary copy-placeholder"
                                            data-token="{{ $ph }}" title="Copy {{ $ph }}">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <form action="{{ route('order-email-templates.store') }}" method="POST" id="emailTemplateForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Create Email Template</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Title --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="title" class="form-label">Template Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title') }}"
                                        placeholder="Enter template title, e.g., Order Confirmation">
                                    <small class="text-muted">This will be visible in the template list.</small>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            {{-- Order Status --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="order_status_id" class="form-label">Order Status<span
                                            class="text-danger">*</span></label>
                                    <select name="order_status_id" id="order_status_id" class="form-select">
                                        <option value="">-- Select Order Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ old('order_status_id') == $status->id ? 'selected' : '' }}>
                                                {{ !empty($isPickupParam) && $isPickupParam ? $status->pickup_name : $status->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="is_pickup"
                                        value="{{ old('is_pickup', !empty($isPickupParam) && $isPickupParam ? 1 : 0) }}">

                                    <small class="text-muted">If selected, this template will be sent when the order
                                        reaches
                                        this status.</small>
                                    @error('order_status_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            {{-- Subject --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="subject" class="form-label">Email Subject <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="subject" id="subject" class="form-control"
                                        value="{{ old('subject') }}"
                                        placeholder="Enter email subject, e.g., Your order #12345 has been confirmed">
                                    @error('subject')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="col-md-12 mb-3">
                                <div class="box_label">
                                    <label for="body" class="form-label">Email Body <span
                                            class="text-danger">*</span></label>
                                    <textarea name="body" id="body" class="form-control" rows="8"
                                        placeholder="Use placeholders like {customer_name}, {order_id}, {order_list}, {total_order_value},{order_details_url_button} ">{{ old('body') }}</textarea>
                                    <small class="text-muted">HTML is allowed. Use placeholders: {customer_name},
                                        {customer_email}, {order_list}, {order_id}, {arriving_date},
                                        {total_order_value},{order_details_url_button}</small>
                                    @error('body')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Is Active --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select name="is_active" id="is_active" class="form-select">
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="w-100 text-end mt-3">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ route('order-email-templates.index') }}" class="btn btn-primary">Cancel</a>
                            </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>
    <script>
        ClassicEditor.create(document.querySelector("#body"));
    </script>
    <script>
        $(document).ready(function() {
            $("#emailTemplateForm").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
    <script>
        document.addEventListener('click', function(e) {
            // Insert into editor
            if (e.target.closest('.placeholder-chip') && !e.target.closest('.copy-placeholder')) {
                const token = e.target.closest('.placeholder-chip').dataset.token;

                // replace 'tiny' with 'ckeditor' logic depending on your editor
                // --- TinyMCE ---
                if (window.tinymce) {
                    const ed = tinymce.activeEditor;
                    if (ed) {
                        ed.focus();
                        ed.selection.setContent(token);
                        return;
                    }
                }

                // --- CKEditor 4 ---
                if (window.CKEDITOR && CKEDITOR.instances && Object.keys(CKEDITOR.instances).length) {
                    const instance = CKEDITOR.instances['body'] || Object.values(CKEDITOR.instances)[0];
                    if (instance) {
                        instance.focus();
                        instance.insertHtml(token);
                        return;
                    }
                }

                // --- CKEditor 5 (classic) ---
                if (window.ClassicEditor && window.bodyEditorInstance) { // if you stored instance
                    window.bodyEditorInstance.model.change(writer => {
                        const insertPosition = window.bodyEditorInstance.model.document.selection
                            .getFirstPosition();
                        writer.insertText(token, insertPosition);
                    });
                    return;
                }

                // fallback: plain textarea with id="body"
                const ta = document.getElementById('body');
                if (ta) insertAtCursor(ta, token);
            }

            // Copy token
            if (e.target.closest('.copy-placeholder')) {
                const btn = e.target.closest('.copy-placeholder');
                const token = btn.dataset.token;
                navigator.clipboard?.writeText(token).then(() => {
                    btn.innerHTML = '<i class="fa fa-check"></i>';
                    setTimeout(() => btn.innerHTML = '<i class="fa fa-copy"></i>', 800);
                }).catch(() => alert('Copy failed — token: ' + token));
            }
        });

        // same helper as Option A
        function insertAtCursor(el, text) {
            if (document.selection) {
                el.focus();
                var sel = document.selection.createRange();
                sel.text = text;
            } else if (el.selectionStart || el.selectionStart === 0) {
                var startPos = el.selectionStart;
                var endPos = el.selectionEnd;
                var before = el.value.substring(0, startPos);
                var after = el.value.substring(endPos, el.value.length);
                el.value = before + text + after;
                const pos = startPos + text.length;
                el.selectionStart = el.selectionEnd = pos;
            } else {
                el.value += text;
            }
        }
    </script>

    <style>
        .placeholder-chip {
            gap: 6px;
            padding: 6px 8px;
            border-radius: 6px;
        }

        .placeholder-chip code {
            background: transparent;
            color: #333;
            font-weight: 600;
        }

        .copy-placeholder {
            padding: 0 6px;
            border-left: 1px solid rgba(0, 0, 0, 0.06);
            margin-left: 4px;
        }
    </style>
@endpush
