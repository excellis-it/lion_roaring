@extends('user.layouts.master')
@section('title')
    User Activity - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                    </div>
                                    <div class="col-md-2 float-right">
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Activity List</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th></th>
                                                <th>User Name</th>
                                                <th>Email</th>
                                                <th>User Role</th>
                                                <th>Ecclesia Name</th>
                                                <th>IP</th>
                                                <th>Country Code</th>
                                                <th>Country Name</th>
                                                {{-- <th>Device MAC</th> --}}
                                                <th>Device Type</th>
                                                <th>Browser</th>
                                                <th>URL</th>
                                                {{-- <th>Permission Access</th> --}}
                                                <th>Activity Type</th>
                                                <th>Activity Description</th>
                                                <th>Activity Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($activity) > 0)
                                                @foreach ($activity as $key => $act)
                                                    <tr>
                                                        {{-- <td>{{ $act->user_id }}</td> --}}
                                                        <td>{{ $activity->firstItem() + $key }}</td>
                                                        <td>{{ $act->user_name }}</td>
                                                        <td>{{ $act->email }}</td>
                                                        <td>{{ $act->user_roles }}</td>
                                                        <td>{{ $act->ecclesia_name }}</td>
                                                        <td>{{ $act->ip }}</td>
                                                        <td>{{ $act->country_code }}</td>
                                                        <td>{{ $act->country_name }}</td>
                                                        {{-- <td>{{ $act->device_mac }}</td> --}}
                                                        <td>{{ $act->device_type }}</td>
                                                        <td>{{ $act->browser }}</td>
                                                        <td>{{ $act->url }}</td>
                                                        {{-- <td>{{ $act->permission_access }}</td> --}}
                                                        <td>{{ $act->activity_type }}</td>
                                                        <td>{{ $act->activity_description }}</td>
                                                        <td>{{ $act->activity_date }}</td>
                                                    </tr>
                                                @endforeach
                                                {{-- pagination --}}
                                                <tr class="toxic">
                                                    <td colspan="16">
                                                        <div class="d-flex justify-content-center">
                                                            {!! $activity->links() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="toxic">
                                                    <td colspan="16" class="text-center">No Data Found</td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>
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
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this activity.",
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
