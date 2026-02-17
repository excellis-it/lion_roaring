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
                                        @if (auth()->user()->can('Edit Email Template'))
                                            <p class="text-info small mb-0"><i class="fa-solid fa-info-circle"></i>
                                                <strong>Tip:</strong> Drag and drop rows using the <i
                                                    class="fa-solid fa-grip-vertical"></i> handle to reorder templates.
                                            </p>
                                        @endif
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
                                    <table id="generalTemplatesTable" class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th style="width: 30px;"></th>
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
                                                <tr data-id="{{ $template->id }}">
                                                    <td>
                                                        @if (auth()->user()->can('Edit Email Template'))
                                                            <i class="fa-solid fa-grip-vertical drag-handle"></i>
                                                        @endif
                                                    </td>
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
                                                    <td colspan="8" class="text-center">No email templates found</td>
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
                                        <table id="pickupTemplatesTable"
                                            class="table align-middle bg-white color_body_text">
                                            <thead class="color_head">
                                                <tr class="header-row">
                                                    <th style="width: 30px;"></th>
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
                                                    <tr data-id="{{ $template->id }}">
                                                        <td>
                                                            @if (auth()->user()->can('Edit Email Template'))
                                                                <i class="fa-solid fa-grip-vertical drag-handle"></i>
                                                            @endif
                                                        </td>
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
                                                        <td colspan="8" class="text-center">No pickup templates found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>

                                    <div class="row mb-2 mt-5">
                                        <div class="col-md-10">
                                            <h3 class="mb-3">Digital Product Email Template List</h3>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="table-responsive">
                                            <table id="digitalTemplatesTable"
                                                class="table align-middle bg-white color_body_text">
                                                <thead class="color_head">
                                                    <tr class="header-row">
                                                        <th style="width: 30px;"></th>
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
                                                    @if ($digitalTemplate)
                                                        <tr data-id="{{ $digitalTemplate->id }}">
                                                            <td></td>
                                                            <td>1</td>
                                                            <td>{{ $digitalTemplate->title }}</td>
                                                            <td>{{ $digitalTemplate->slug }}</td>
                                                            <td>{{ $digitalTemplate->orderStatus ? $digitalTemplate->orderStatus->name : '-' }}
                                                            </td>
                                                            <td>{{ $digitalTemplate->subject }}</td>
                                                            <td>{{ $digitalTemplate->is_active ? 'Active' : 'Inactive' }}
                                                            </td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    @if (auth()->user()->can('Edit Email Template'))
                                                                        <a href="{{ route('order-email-templates.edit', $digitalTemplate->id) }}"
                                                                            class="edit_icon me-2">
                                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="8" class="text-center">No digital templates
                                                                found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
    {{-- SortableJS for drag-and-drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        $(document).ready(function() {
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

            // Initialize drag-and-drop for general templates
            var generalTableBody = document.querySelector('#generalTemplatesTable tbody');
            if (generalTableBody) {
                var generalSortable = Sortable.create(generalTableBody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        updateOrder('general');
                    }
                });
            }

            // Initialize drag-and-drop for pickup templates
            var pickupTableBody = document.querySelector('#pickupTemplatesTable tbody');
            if (pickupTableBody) {
                var pickupSortable = Sortable.create(pickupTableBody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        updateOrder('pickup');
                    }
                });
            }

            function updateOrder(type) {
                var tbody = type === 'general' ? '#generalTemplatesTable tbody' : '#pickupTemplatesTable tbody';
                var order = [];

                $(tbody + ' tr').each(function(index) {
                    var id = $(this).data('id');
                    if (id) {
                        order.push(id);
                    }
                });

                $.ajax({
                    url: '{{ route('order-email-templates.update-order') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update row numbers (second column, not first which is the drag handle)
                            $(tbody + ' tr').each(function(index) {
                                $(this).find('td:nth-child(2)').text(index + 1);
                            });

                            // Show success message
                            toastr.success('Order updated successfully');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update order');
                        console.error('Error updating order:', xhr);
                    }
                });
            }
        });
    </script>

    <style>
        .drag-handle {
            cursor: move;
            color: #6c757d;
            padding: 5px;
            transition: color 0.2s;
        }

        .drag-handle:hover {
            color: #495057;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }

        .sortable-chosen {
            background: #e9ecef;
        }

        .sortable-drag {
            opacity: 1;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush
