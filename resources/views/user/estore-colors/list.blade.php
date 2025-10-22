@extends('user.layouts.master')

@section('title')
    E-Store Color Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Color List</h3>
                </div>
                <div class="col-md-2 float-right">

                    @if (auth()->user()->can('Create Estore Colors'))
                        <a href="{{ route('colors.create') }}" class="btn btn-primary w-100"><i class="fa-solid fa-upload"></i>
                            Create Color</a>
                    @endif

                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            {{-- <th>Color</th> --}}
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($colors as $key => $color)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $color->color_name }}</td>
                                {{-- <td>
                                    <div style="width: 60px; height: 25px; background-color: {{ $color->color }};"></div>
                                </td> --}}
                                <td class="d-flex">
                                    @if (auth()->user()->can('Edit Estore Colors'))
                                        <a href="{{ route('colors.edit', $color->id) }}" class="edit_icon me-2"><i
                                                class="fa-solid fa-edit"></i></a>
                                    @endif
                                    <a href="javascript:void(0)" id="delete"
                                        data-route="{{ route('colors.delete', $color->id) }}" class="delete_icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
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
                    text: "To delete this.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        console.log('delete clicked');
                        var route = $(this).data('route');
                        $.ajax({
                            url: route,
                            type: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    swal("Deleted!", response.msg, "success")
                                        .then(() => {
                                            location.reload();
                                        });
                                } else {
                                    swal("Info", response.msg, "info");
                                }
                            },
                            error: function(xhr) {
                                swal("Error!", "An error occurred while deleting the color.",
                                    "error");
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        // swal(
                        //     'Cancelled',
                        //     'Your stay here :)',
                        //     'error'
                        // )
                    }
                })
        });
    </script>
@endpush
