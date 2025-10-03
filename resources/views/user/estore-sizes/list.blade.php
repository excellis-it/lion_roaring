@extends('user.layouts.master')

@section('title')
    E-Store Size Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Sizes List</h3>
                </div>
                <div class="col-md-2 float-right">

                    @if (auth()->user()->can('Create Estore Sizes'))
                        <a href="{{ route('sizes.create') }}" class="btn btn-primary w-100"><i class="fa-solid fa-upload"></i>
                            Create Size</a>
                    @endif

                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sizes as $key => $size)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $size->size }}</td>
                                <td class="d-flex">
                                    @if (auth()->user()->can('Edit Estore Sizes'))
                                        <a href="{{ route('sizes.edit', $size->id) }}" class="edit_icon me-2"><i
                                                class="fa-solid fa-edit"></i></a>
                                    @endif
                                    {{-- <a href="javascript:void(0)" id="delete"
                                        data-route="{{ route('sizes.delete', $size->id) }}" class="delete_icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this file.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        window.location = $(this).data('route');
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        });
    </script>
@endpush
