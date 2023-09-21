@extends('admin.layouts.master')
@section('title')
    All Customer Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush
@section('head')
    All Customer Details
@endsection
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="">
                    <div class="row justify-content-between">
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Name</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        placeholder="Full Name" value="Full Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Phone Number</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        placeholder="Phone Number" value="Phone Number">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Email ID</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        placeholder="Email ID" value="Email ID">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Password</label>
                                    <input type="text" class="form-control" id="floatingInputValue"
                                        placeholder="Password" value="Password">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button>save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card table_sec stuff-list-table">
                <div class="table-responsive">
                    <table class="table table-bordered" id="example" class="display">
                        <thead>
                            <tr>
                                <th> Name</th>
                                <th> Email</th>
                                <th> Phone</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $key => $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>{{ $customer->country }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>
                                        <div class="button-switch">
                                            <input type="checkbox" id="switch-orange" class="switch toggle-class"
                                                data-id="{{ $customer['id'] }}"
                                                {{ $customer['status'] ? 'checked' : '' }} />
                                            <label for="switch-orange" class="lbl-off"></label>
                                            <label for="switch-orange" class="lbl-on"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="edit-1 d-flex align-items-center justify-content-center">
                                            <a href=""> <span class="edit-icon"><i
                                                        class="ph ph-pencil-simple"></i></span></a>
                                            <a href=""> <span class="trash-icon"><i
                                                        class="ph ph-trash"></i></span></a>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            //Default data table
            $('#myTable').DataTable({
                "aaSorting": [],
                "columnDefs": [{
                        "orderable": false,
                        "targets": [6, 7]
                    },
                    {
                        "orderable": true,
                        "targets": [0, 1, 2, 3, 4, 5]
                    }
                ]
            });

        });
    </script>
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this customer.",
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
    <script>
        $('.toggle-class').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('customers.change-status') }}',
                data: {
                    'status': status,
                    'user_id': user_id
                },
                success: function(resp) {
                    console.log(resp.success)
                }
            });
        });
    </script>
@endpush
<div class="main-content">
    <div class="inner_page">
        <div class="card search_bar sales-report-card">
            <form action="">
                <div class="row justify-content-between">
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Name</label>
                                <input type="text" class="form-control" id="floatingInputValue"
                                    placeholder="Full Name" value="Full Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Phone Number</label>
                                <input type="text" class="form-control" id="floatingInputValue"
                                    placeholder="Phone Number" value="Phone Number">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Email ID</label>
                                <input type="text" class="form-control" id="floatingInputValue"
                                    placeholder="Email ID" value="Email ID">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Password</label>
                                <input type="text" class="form-control" id="floatingInputValue"
                                    placeholder="Password" value="Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="btn-1">
                            <button>save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card table_sec stuff-list-table">
            <div class="table-responsive">
                <table class="table table-bordered" id="example" class="display">
                    <thead>
                        <tr>
                            <th> Name</th>
                            <th> Email</th>
                            <th> Phone</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $key => $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->city }}</td>
                                <td>{{ $customer->country }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>
                                    <div class="edit-1 d-flex align-items-center justify-content-center">
                                        <a href=""> <span class="edit-icon"><i
                                                    class="ph ph-pencil-simple"></i></span></a>
                                        <a href=""> <span class="trash-icon"><i
                                                    class="ph ph-trash"></i></span></a>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
