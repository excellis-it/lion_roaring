@extends('user.layouts.master')
@section('title')
    Manage Keywords - Chatbot Assistant
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="dashboard-top-heading d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>Chatbot Keywords</h3>
                    <p class="text-muted">Manage automatic responses based on user input</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="print_btn" data-bs-toggle="modal"
                        data-bs-target="#bulkUploadModal">
                        <i class="fas fa-upload me-1"></i> Bulk Upload
                    </button>
                    <a href="{{ route('user.admin.chatbot.keywords.create') }}" class="print_btn">+ New Keyword</a>
                </div>
            </div>

            <!-- Bulk Upload Modal -->
            <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Keywords</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('user.admin.chatbot.keywords.bulk-upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="csv_file" class="form-label">Select File (CSV or Excel)</label>
                                    <input type="file" name="csv_file" id="csv_file" class="form-control"
                                        accept=".csv,.txt,.xlsx,.xls" required>
                                    <div class="form-text mt-2">
                                        File should have three columns: <strong>Keyword</strong>,
                                        <strong>Response</strong>, and <strong>Search Type</strong> (others, estore, or
                                        elearning).<br>
                                        The first row (header) will be skipped. Supported formats: .csv, .txt, .xlsx, .xls
                                        <div class="mt-3">
                                            <a href="{{ route('user.admin.chatbot.keywords.sample-download') }}"
                                                class="text-primary fw-bold">
                                                <i class="fas fa-download me-1"></i> Download Sample CSV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="print_btn">Upload & Process</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive mt-3">
                <table class="table align-middle bg-white">
                    <thead class="color_head">
                        <tr>
                            <th>Keyword</th>
                            <th>Type</th>
                            <th>Response Preview</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($keywords as $keyword)
                            <tr>
                                <td class="fw-bold">{{ $keyword->keyword }}</td>
                                <td>
                                    @if ($keyword->search_type == 'estore')
                                        <span class="badge bg-info">E-store</span>
                                    @elseif($keyword->search_type == 'elearning')
                                        <span class="badge bg-primary">E-learning</span>
                                    @else
                                        <span class="badge bg-secondary">Others</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit(strip_tags($keyword->response), 80) }}</td>
                                <td>{{ $keyword->usage_count }}</td>
                                <td>
                                    <span class="badge {{ $keyword->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $keyword->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.admin.chatbot.keywords.edit', $keyword->id) }}" class="p-2"
                                        title="Edit"><i class="fas fa-edit text-primary"></i></a>
                                    <a href="javascript:void(0)"
                                        onclick="confirmDelete('{{ route('user.admin.chatbot.keywords.delete', $keyword->id) }}')"
                                        class="p-2" title="Delete"><i class="fas fa-trash text-danger"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $keywords->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(url) {
                swal({
                    title: "Are you sure?",
                    text: "You want to delete this keyword response!",
                    type: "warning",
                    confirmButtonText: "Yes, delete it",
                    showCancelButton: true
                }).then((result) => {
                    if (result.value) {
                        window.location.href = url;
                    }
                });
            }
        </script>
    @endpush
@endsection
