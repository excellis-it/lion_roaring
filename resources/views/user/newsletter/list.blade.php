@extends('user.layouts.master')
@section('title')
    Newsletter List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <h3 class="mb-3">Newsletter List</h3>
                                </div>
                                <div class="col-md-2 float-right">
                                </div>
                            </div>
                            <div class="row justify-content-end mb-2">
                                <div class="col-lg-4">
                                    <div class="search-field d-flex">
                                        <input type="text" name="search" id="search" placeholder="search..."
                                            class="form-control rounded_search">
                                        <button class="submit_search" id="search-button"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <select id="page_size" class="form-control">
                                        <option value="10" selected>10 per page</option>
                                        <option value="25">25 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 text-end">
                                    <button type="button" id="openSendModalBtn" class="btn btn-primary w-100">Send
                                        Email</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle bg-white color_body_text">
                                    <thead class="color_head">
                                        <tr class="header-row">
                                            <th><input type="checkbox" id="select_all_rows"></th>
                                            <th>ID (#)</th>
                                            <th class="sorting" data-tippy-content="Sort by Email" data-sorting_type="desc"
                                                data-column_name="email" style="cursor: pointer">Email <span
                                                    id="email_icon"><i class="fa fa-arrow-down"></i></span></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="newsletters_tbody">
                                        @include('user.newsletter.table', [
                                            'newsletters' => $newsletters,
                                        ])
                                    </tbody>
                                </table>
                                <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                                <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc" />
                            </div>

                            {{-- Send Email Modal --}}
                            <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form id="sendEmailForm">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Send Email to Selected</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="selectedCount" class="mb-2"></div>

                                                <div class="mb-3">
                                                    <label for="email_subject" class="form-label">Subject</label>
                                                    <input type="text" id="email_subject" name="subject"
                                                        class="form-control">
                                                    <small class="text-danger field-error" style="color: red;"
                                                        data-field="subject"></small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email_body" class="form-label">Message</label>
                                                    <textarea id="email_body" name="body" rows="8" class="form-control"></textarea>
                                                    <small class="text-danger field-error" style="color: red;"
                                                        data-field="body"></small>
                                                </div>

                                                {{-- hidden field to hold selected ids --}}
                                                <input type="hidden" id="selected_ids" name="selected_ids" value="">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button id="sendEmailsBtn" type="submit" class="btn btn-primary">Send
                                                    Mail</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Include CKEditor CDN (classic) --}}
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#email_body'))
            .catch(error => {
                console.error(error);
            });
        $(function() {
            // initialize CKEditor on textarea
            CKEDITOR.replace('email_body');

            // select/deselect all functionality
            $(document).on('change', '#select_all_rows', function() {
                const checked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', checked);
            });

            // keep header checkbox in sync when single rows changed
            $(document).on('change', '.row-checkbox', function() {
                const total = $('.row-checkbox').length;
                const checked = $('.row-checkbox:checked').length;
                $('#select_all_rows').prop('checked', total === checked);
            });

            // Open modal: collect selected ids and show count
            let emailBodyEditor;

            $('#openSendModalBtn').on('click', function() {
                const ids = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (!ids.length) {
                    alert('Please select at least one recipient.');
                    return;
                }

                $('#selected_ids').val(JSON.stringify(ids));
                $('#selectedCount').text(ids.length + ' recipient(s) selected.');

                // Clear previous errors and fields
                $('.field-error').text('');
                $('#email_subject').val('');

                // Destroy existing CKEditor if exists
                if (emailBodyEditor) {
                    emailBodyEditor.destroy(true);
                }

                // Initialize CKEditor 4 inside modal
                emailBodyEditor = CKEDITOR.replace('email_body');

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('sendEmailModal'));
                modal.show();
            });


            // Submit send form via AJAX
            $('#sendEmailForm').on('submit', function(e) {
                e.preventDefault();
                $('.field-error').text('');
                $('#sendEmailsBtn').prop('disabled', true);

                const subject = $('#email_subject').val().trim();
                // const body = emailBodyEditor ? emailBodyEditor.getData().trim() : '';
                const body = $('#email_body').text();
                const ids = JSON.parse($('#selected_ids').val() || '[]');

                if (!subject) {
                    $('[data-field="subject"]').text('Subject is required.');
                    $('#sendEmailsBtn').prop('disabled', false);
                    return;
                }
                if (!body) {
                    $('[data-field="body"]').text('Message is required.');
                    $('#sendEmailsBtn').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ route('user.newsletters.send-mail') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        selected_ids: ids,
                        subject: subject,
                        body: body
                    },
                    success: function(res) {
                        const modalEl = document.getElementById('sendEmailModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        alert(res.message || 'Emails queued successfully.');
                        $('#sendEmailsBtn').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#sendEmailsBtn').prop('disabled', false);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            if (errors.subject) $('[data-field="subject"]').text(errors.subject[
                                0]);
                            if (errors.body) $('[data-field="body"]').text(errors.body[0]);
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    }
                });
            });



            // If you reload rows via AJAX (search/pagination), re-check header checkbox state
            // Example: after reloading table you might call:
            // $('#select_all_rows').prop('checked', false);
        });
    </script>


    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this newsletter.",
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
        $(document).ready(function() {

            function clear_icon() {
                $('#name_icon').html('');
                $('#email_icon').html('');
                $('#message_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query, topic_id, per_page) {
                $.ajax({
                    url: "{{ route('user.newsletters.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query,
                        topic_id: topic_id,
                        per_page: per_page
                    },
                    success: function(data) {
                        $('tbody').html(data.data);
                    }
                });
            }

            // Search
            $(document).on('keyup', '#search', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                var topic_id = $('#topics').val();
                var per_page = $('#page_size').val();
                fetch_data(page, sort_type, column_name, query, topic_id, per_page);
            });

            // Sorting
            $(document).on('click', '.sorting', function() {
                var column_name = $(this).data('column_name');
                var order_type = $(this).data('sorting_type');
                var reverse_order = '';

                if (order_type == 'asc') {
                    $(this).data('sorting_type', 'desc');
                    reverse_order = 'desc';
                    clear_icon();
                    $('#' + column_name + '_icon').html('<i class="fa fa-arrow-down"></i>');
                } else {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html('<i class="fa fa-arrow-up"></i>');
                }

                $('#hidden_column_name').val(column_name);
                $('#hidden_sort_type').val(reverse_order);
                var page = $('#hidden_page').val();
                var query = $('#search').val();
                var topic_id = $('#topics').val();
                var per_page = $('#page_size').val();
                fetch_data(page, reverse_order, column_name, query, topic_id, per_page);
            });

            // Pagination
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                $('#hidden_page').val(page);

                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var query = $('#search').val();
                var topic_id = $('#topics').val();
                var per_page = $('#page_size').val();

                $('li').removeClass('active');
                $(this).parent().addClass('active');
                fetch_data(page, sort_type, column_name, query, topic_id, per_page);
            });

            // Topic Filter
            $(document).on('change', '#topics', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                var topic_id = $(this).val();
                var per_page = $('#page_size').val();
                fetch_data(page, sort_type, column_name, query, topic_id, per_page);
            });

            // Page Size Filter
            $(document).on('change', '#page_size', function() {
                var per_page = $(this).val();
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = 1; // reset to first page
                var topic_id = $('#topics').val();
                $('#hidden_page').val(page);
                fetch_data(page, sort_type, column_name, query, topic_id, per_page);
            });

        });
    </script>
@endpush
