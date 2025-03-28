@extends('user.layouts.master')
@section('title')
    Sent - Email - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <!-- Main Body Starts -->
            <div class="main__body">
                <!-- Sidebar Starts -->
                @include('user.mail.partials.sidebar')
                <!-- Sidebar Ends -->
                <!-- Email List Starts -->
                <div class="emailList">
                    <!-- Settings Starts -->
                    <div class="emailList__settings">
                        <div class="emailList__settingsLeft">
                            <input type="checkbox" id="selectAll" />
                            <span type="button" class="material-symbols-outlined"> arrow_drop_down </span>
                            <span onclick="fetchSentEmails()" type="button" class="material-symbols-outlined"> refresh
                            </span>
                            <span type="button" class="material-symbols-outlined" id="delete-sent"> delete </span>
                        </div>
                        <div class="emailList__settingsRight d-flex">
                            <span type="button" id="mailListPrevPage" class="material-symbols-outlined">chevron_left</span>
                            <span id="paginationInfo"></span>
                            <span type="button" id="mailListNextPage" class="material-symbols-outlined">chevron_right</span>
                        </div>
                    </div>
                    <!-- Settings Ends -->


                    <div class="emailList__list" id="sent-email-list-{{ auth()->id() }}">
                        {{-- @include('user.mail.partials.inbox-email-list') --}}

                    </div>
                </div>
            </div>

            @include('user.mail.partials.create-mail')


        </div>


    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
           // let currentMailPage_sent = 1;
            fetchSentEmails();

            $('#mailListPrevPage').on('click', function() {
                if (currentMailPage_sent > 1) {
                    fetchSentEmails(currentMailPage_sent - 1);
                }
            });

            $('#mailListNextPage').on('click', function() {
                fetchSentEmails(currentMailPage_sent + 1);
            });

            // When the "select all" checkbox is clicked
            $(document).on('click', '#selectAll', function() {
                // Check or uncheck all the mail checkboxes based on the "select all" checkbox
                $('.selectMail').prop('checked', this.checked);
            });

            // Optional: If any individual checkbox is unchecked, uncheck the "select all" checkbox
            $('.selectMail').on('change', function() {
                if ($('.selectMail:checked').length !== $('.selectMail').length) {
                    $('#selectAll').prop('checked', false);
                } else {
                    $('#selectAll').prop('checked', true);
                }
            });
        });
    </script>
    <script>
        //view mail
        $(document).on('click', '.view-mail', function(e) {
            e.preventDefault();
            var route = $(this).data('route');
            window.location.href = route;
        });
    </script>

    <script>
        // delete checked mail
        $(document).ready(function() {
            $(document).on('click', '#delete-sent', function(e) {
                e.preventDefault();
                var mailIds = [];
                $('.selectMail:checked').each(function() {
                    mailIds.push($(this).data('id'));
                });

                if (mailIds.length === 0) {
                    swal('Error', 'Please select at least one mail to delete', 'error');
                    return;
                }

                swal({
                        title: "Are you sure?",
                        text: "To remove this mail",
                        type: "warning",
                        confirmButtonText: "Yes",
                        showCancelButton: true
                    })
                    .then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: "{{ route('mail.deleteSentsMail') }}",
                                type: 'POST',
                                data: {
                                    mailIds: mailIds
                                },
                                success: function(response) {
                                    if (response.status === true) {
                                        toastr.success(response.message);
                                        fetchSentEmails();
                                    } else {
                                        swal('Error', response.message, 'error');
                                    }
                                }
                            });
                        } else if (result.dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your stay here :)',
                                'error'
                            )
                        }
                    })
            });


        });
    </script>
@endpush
