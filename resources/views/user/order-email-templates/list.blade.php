@extends('user.layouts.master')
@section('title')
    Email Template List - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3">Email Template List</h3>
                                        <p class="text-muted small mb-2">Below are templates for delivery-related order
                                            statuses. Pickup templates are shown separately below.</p>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Email Template'))
                                            <a href="{{ route('order-email-templates.create') }}"
                                                class="btn btn-primary w-100"><i class="fa-solid fa-plus"></i> Create
                                                Email Template</a>

                                        @endif
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th>Title</th>
                                                <th>Slug</th>
                                                <th>Order Status</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($generalTemplates as $key => $template)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $template->title }}</td>
                                                    <td>{{ $template->slug }}</td>
                                                    <td>{{ $template->orderStatus ? $template->orderStatus->name : '-' }}
                                                    </td>
                                                    <td>{{ $template->subject }}</td>
                                                    <td>{{ $template->is_active ? 'Active' : 'Inactive' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            @if (auth()->user()->can('Edit Email Template'))
                                                                <a href="{{ route('order-email-templates.edit', $template->id) }}"
                                                                    class="edit_icon me-2">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                </a>
                                                            @endif

                                                            @if (auth()->user()->can('Delete Email Template') &&
                                                                    !in_array($template->orderStatus?->slug, ['pending', 'delivered', 'cancelled']))
                                                                <a href="javascript:void(0)" class="delete_template"
                                                                    data-route="{{ route('order-email-templates.destroy', $template->id) }}">
                                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No email templates found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <hr>

                                <div class="row mb-2 mt-5">
                                    <div class="col-md-10">
                                        <h3 class="mb-3">Pickup Email Template List</h3>

                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Email Template'))
                                            <a href="{{ route('order-email-templates.create', ['type' => 'pickup']) }}"
                                                class="btn btn-primary w-100 mt-2"><i class="fa-solid fa-plus"></i>
                                                Create Pickup Email Template</a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Pickup templates table --}}
                                <div class="mt-3">

                                    <div class="table-responsive">
                                        <table class="table align-middle bg-white color_body_text">
                                            <thead class="color_head">
                                                <tr class="header-row">
                                                    <th>ID (#)</th>
                                                    <th>Title</th>
                                                    <th>Slug</th>
                                                    <th>Order Status</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($pickupTemplates as $key => $template)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $template->title }}</td>
                                                        <td>{{ $template->slug }}</td>
                                                        <td>{{ $template->orderStatus ? $template->orderStatus->pickup_name ?? $template->orderStatus->name : '-' }}
                                                        </td>
                                                        <td>{{ $template->subject }}</td>
                                                        <td>{{ $template->is_active ? 'Active' : 'Inactive' }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                @if (auth()->user()->can('Edit Email Template'))
                                                                    <a href="{{ route('order-email-templates.edit', $template->id) }}"
                                                                        class="edit_icon me-2">
                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                    </a>
                                                                @endif

                                                                @if (auth()->user()->can('Delete Email Template') &&
                                                                        !in_array($template->orderStatus?->slug, ['pending', 'delivered', 'cancelled']))
                                                                    <a href="javascript:void(0)" class="delete_template"
                                                                        data-route="{{ route('order-email-templates.destroy', $template->id) }}">
                                                                        <i class="fa-solid fa-trash text-danger"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No pickup templates found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
        $(document).on('click', '.delete_template', function() {
            var route = $(this).data('route');

            swal({
                title: "Are you sure?",
                text: "You are about to delete this email template.",
                icon: "warning",
                buttons: ["Cancel", "Yes, delete it!"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': route
                    });
                    var token = $('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    });
                    var hiddenMethod = $('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    });
                    form.append(token, hiddenMethod).appendTo('body').submit();
                }
            });
        });
    </script>
@endpush
