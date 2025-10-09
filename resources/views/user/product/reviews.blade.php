@extends('user.layouts.master')

@section('title')
    Product Reviews - {{ $product->name }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div>
                                <h3 class="mb-1">Product Reviews</h3>
                                <p class="mb-0 text-muted">
                                    <strong>{{ $product->name }}</strong>
                                    {{-- <span class="text-muted">&middot; SKU:</span> {{ $product->sku ?? 'N/A' }} --}}
                                </p>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-arrow-left-long me-1"></i> Back to Products
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- <form method="GET" action="{{ route('user.store-products.reviews', $product->id) }}"
                                class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="status-filter" class="form-label">Status</label>
                                    <select name="status" id="status-filter" class="form-select">
                                        <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>All Status
                                        </option>
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ (string) $statusFilter === (string) $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                        value="{{ $searchTerm }}"
                                        placeholder="Search by reviewer name, email or comment">
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-md-end">
                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('user.store-products.reviews', $product->id) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form> --}}

                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reviewer</th>
                                            <th>Rating</th>
                                            <th>Review</th>
                                            <th>Status</th>
                                            <th>Submitted</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reviews as $review)
                                            <tr>
                                                <td>{{ $review->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        {{-- <div
                                                            class="avatar avatar-sm bg-primary-subtle text-primary fw-semibold me-2">
                                                            {{ strtoupper(substr($review->user->full_name ?? 'U', 0, 1)) }}
                                                        </div> --}}
                                                        <div>
                                                            <div class="fw-semibold">
                                                                {{ $review->user->full_name ?? 'Unknown User' }}</div>
                                                            <div class="text-muted small">
                                                                {{ $review->user->email ?? 'No email' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fa-solid fa-star me-1"></i>{{ $review->rating }} / 5
                                                    </span>
                                                </td>
                                                <td style="max-width: 320px;">
                                                    {!! nl2br(e(\Illuminate\Support\Str::limit($review->review, 200))) !!}
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass =
                                                            $statusBadgeClasses[$review->status] ?? 'bg-secondary';
                                                    @endphp
                                                    <span
                                                        class="">{{ $review->status_label }}</span>
                                                </td>
                                                <td>{{ $review->created_at?->format('d M Y, h:i A') }}</td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-2">
                                                        @if ($review->status != \App\Models\Review::STATUS_APPROVED && auth()->user()->can('Edit Estore Products'))
                                                            <form
                                                                action="{{ route('user.store-products.reviews.approve', ['product' => $product->id, 'review' => $review->id]) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Approve this review?');">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn edit_icon">
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if (auth()->user()->can('Delete Estore Products'))
                                                            <form
                                                                action="{{ route('user.store-products.reviews.delete', ['product' => $product->id, 'review' => $review->id]) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Delete this review?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn delete_icon">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="text-muted">No reviews found for this product.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                {{ $reviews->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
