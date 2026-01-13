@extends('user.layouts.master')
@section('title')
    {{ isset($keyword) ? 'Edit Keyword' : 'Create Keyword' }} - Chatbot Assistant
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="dashboard-top-heading mb-4">
                <h3>{{ isset($keyword) ? 'Edit Chatbot Keyword' : 'Create Chatbot Keyword' }}</h3>
                <p class="text-muted">Define how the chatbot should respond to specific words or phrases</p>
            </div>

            <form
                action="{{ isset($keyword) ? route('user.admin.chatbot.keywords.update', $keyword->id) : route('user.admin.chatbot.keywords.store') }}"
                method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm p-4">
                            <div class="mb-4">
                                <label for="keyword" class="form-label fw-bold">Keyword / Pattern</label>
                                <input type="text" name="keyword" id="keyword"
                                    class="form-control @error('keyword') is-invalid @enderror"
                                    value="{{ old('keyword', $keyword->keyword ?? '') }}"
                                    placeholder="e.g. shipping, membership, help" required>
                                <div class="form-text text-muted">The chatbot uses a "contains" match. Longer patterns take
                                    precedence.</div>
                                @error('keyword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="search_type" class="form-label fw-bold">Search Type / Action</label>
                                <select name="search_type" id="search_type"
                                    class="form-control @error('search_type') is-invalid @enderror" required>
                                    <option value="others"
                                        {{ old('search_type', $keyword->search_type ?? 'others') == 'others' ? 'selected' : '' }}>
                                        Others (Automated Response)</option>
                                    <option value="estore"
                                        {{ old('search_type', $keyword->search_type ?? 'others') == 'estore' ? 'selected' : '' }}>
                                        E-store (Product Search)</option>
                                    <option value="elearning"
                                        {{ old('search_type', $keyword->search_type ?? 'others') == 'elearning' ? 'selected' : '' }}>
                                        E-learning (Course Search)</option>
                                </select>
                                <div class="form-text text-muted">Choose what action the bot should take when this keyword
                                    is matched.</div>
                                @error('search_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="response" class="form-label fw-bold">Bot Response</label>
                                <textarea name="response" id="response" rows="6"
                                    class="form-control description @error('response') is-invalid @enderror" placeholder="What should the bot say?">{{ old('response', $keyword->response ?? '') }}</textarea>
                                <div class="form-text text-muted">Use the editor to add links, formatting, or lists.</div>
                                @error('response')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit"
                                    class="print_btn px-4">{{ isset($keyword) ? 'Update Response' : 'Create Response' }}</button>
                                <a href="{{ route('user.admin.chatbot.keywords') }}"
                                    class="btn btn-outline-secondary px-4">Cancel</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-4 h-100 bg-light">
                            <h5>Options</h5>
                            <hr>
                            <div class="mb-4">
                                <div class="form-check form-switch p-0">
                                    <label class="form-check-label fw-bold d-block mb-3" for="is_active">Status</label>
                                    <div class="form-check form-switch ps-5">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            value="1"
                                            {{ old('is_active', $keyword->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Enable this response</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <div class="p-3 bg-white rounded-3 border">
                                    <small class="text-info d-block mb-2 font-weight-bold uppercase"><i
                                            class="fas fa-info-circle"></i> Tip</small>
                                    <p class="small text-muted mb-0">Rich text is supported. Links will be clickable in the
                                        chat window.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('#response').length) {
                ClassicEditor
                    .create(document.querySelector('#response'))
                    .then(editor => {
                        editor.ui.view.editable.element.style.height = '300px';
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    </script>
@endpush
