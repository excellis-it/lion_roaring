@extends('user.layouts.master')
@section('title')
    Market Materials - {{ env('APP_NAME') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Market Materials</h3>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('market-materials.create') }}" class="btn btn-primary w-100">
                        <i class="fa-solid fa-plus"></i> Add Material
                    </a>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materials as $index => $material)
                            <tr>
                                <td>{{ $materials->firstItem() + $index }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->code }}</td>
                                <td>
                                    <span class="badge {{ $material->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $material->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $material->sort_order }}</td>
                                <td class="text-center">
                                    <a href="{{ route('market-materials.edit', $material->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('market-materials.destroy', $material->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No materials found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
@endsection
