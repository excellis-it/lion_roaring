@if ($countries->count() > 0)
    @foreach ($countries as $country)
        <tr>
            <td>{{ ($countries->currentPage() - 1) * $countries->perPage() + $loop->index + 1 }}</td>
            <td>{{ $country->name }}</td>
            <td>{{ strtoupper($country->code) }}</td>
            <td>
                @if ($country->flag_image)
                    <img src="{{ asset('storage/' . $country->flag_image) }}" alt="flag" width="32" height="22"
                        style="object-fit:cover;border:1px solid #eee;">
                @else
                    —
                @endif
            </td>
            <td>
                @if ($country->languages->count() > 0)
                    @foreach ($country->languages as $language)
                        <span class="badge bg-info text-white me-1 mb-1">{{ $language->name }}</span>
                    @endforeach
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>
                <span data-id="{{ $country->id }}"
                    class="status-span badge {{ $country->status ? 'bg-success' : 'bg-secondary' }}">{{ $country->status ? 'Active' : 'Inactive' }}</span>
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center gap-2 justify-content-end">
                    <form action="{{ route('admin-countries.toggle-status', $country) }}" method="POST"
                        class="d-inline mr-4">
                        @csrf
                        <button class="btn btn-sm btn-outline-warning ms-2 toggle-status" title="Toggle Status"
                            type="button">
                            {{ $country->status ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <a title="Edit" href="{{ route('admin-countries.edit', $country->id) }}">
                        <span class="edit-icon"><i class="fas fa-edit"></i></span>
                    </a>
                    <a title="Delete" data-route="{{ route('admin-countries.delete', $country->id) }}"
                        href="javascript:void(0);" id="delete">
                        <span class="trash-icon"><i class="fas fa-trash"></i></span>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach
    <tr style="box-shadow: none;">
        <td colspan="7">
            <div class="d-flex justify-content-center">
                {!! $countries->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="7" class="text-center">No Countries Found</td>
    </tr>
@endif

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this country.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        window.location = $(this).data('route');
                    } else if (result.dismiss === 'cancel') {
                        swal('Cancelled', 'Your stay here :)', 'error')
                    }
                })
        });

        // ajax toggle-status submit and without page reload affect the status
        $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    swal('Success', 'Country status updated successfully.', 'success');
                    // Update the status text and badge color without reloading the page
                    var statusSpan = $('.status-span[data-id="' + response.id + '"]');
                    if (response.status) {
                        statusSpan.removeClass('bg-secondary').addClass('bg-success').text('Active');
                    } else {
                        statusSpan.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                    }
                    // Update the button text without reloading the page
                    var toggleButton = statusSpan.closest('tr').find('.toggle-status');
                    toggleButton.text(response.status ? 'Deactivate' : 'Activate');
                },
                error: function(xhr) {
                    swal('Error', 'An error occurred while updating status.', 'error');
                }
            });
        });
    </script>
@endpush
