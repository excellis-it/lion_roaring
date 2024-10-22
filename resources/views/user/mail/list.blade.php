@extends('user.layouts.master')
@section('title')
    Email List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
        


        <!-- Main Body Starts -->
    <div class="main__body">
      <!-- Sidebar Starts -->
      <div class="sidebar">
        <button class="sidebar__compose btn_all_open" data-target="#box1"><span class="material-icons"> add </span>Compose</button>
        <div class="sidebarOption sidebarOption__active">
          <span class="material-icons"> inbox </span>
          <h3>Inbox</h3>
        </div>
        <div class="sidebarOption">
          <span class="material-icons"> label_important </span>
          <h3>Important</h3>
        </div>
        <div class="sidebarOption">
          <span class="material-icons"> near_me </span>
          <h3>Sent</h3>
        </div>
        <div class="sidebarOption">
          <span class="material-icons"> note </span>
          <h3>Drafts</h3>
        </div>
      </div>
      <!-- Sidebar Ends -->
      <!-- Email List Starts -->
      <div class="emailList">
        <!-- Settings Starts -->
        <div class="emailList__settings">
          <div class="emailList__settingsLeft">
          <a href=""><span class="material-icons"> arrow_back</span></a>
          <a href=""><span class="material-icons"> refresh </span></a>
          <a href=""><span class="material-icons"> delete </span></a>
          </div>
          <div class="emailList__settingsRight">
          <a href=""><span class="material-icons"> chevron_left </span></a>
          <a href=""><span class="material-icons"> chevron_right </span></a>
          <a href=""><span class="material-icons"> settings </span></a>
          </div>
        </div>
        <!-- Settings Ends -->
         
        <!-- Section Ends -->

        <!-- Email List rows starts -->
        <div class="mail_subject">
            <div class="row">
                <div class="col-lg-9">
                    <h4 class="subject_text_h4">Re: Lorem ipsum dolor sit, amet consectetur adipisicing elit....
                        <span class="inbox_box">inbox <span class="material-icons">close</span></span>
                    </h4>
                </div>
                <div class="col-lg-3 text-end">
                    <a href=""><span class="material-icons">print</span></a>
                </div>
            </div>
        </div>
        <div class="mail_subject">
            <div class="row">
                <div class="col-lg-7">
                    <div class="d-flex">
                        <div class="man_img">
                            <span>
                                <img src="http://127.0.0.1:8000/user_assets/images/logo.png" alt="user" class="user_img">
                            </span>
                        </div>
                        <div class="name_text_p">
                            <h5>Swarnadip Nath</h5>
                            <span class="time_text">To Me</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-end">
                    <div class="d-flex justify-content-end">
                        <span class="time_text">7:10PM (10minutes ago)</span>
                        <a href=""><span class="material-icons">reply</span></a>
                        <a href=""><span class="material-icons">grade</span></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mail_text">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam tempora iure sed, libero sunt, cumque alias porro inventore dolorem, consequuntur culpa nihil iusto rem! Optio reiciendis in dignissimos vel necessitatibus.</p>
        </div>

        <!-- Email List rows Ends -->
      </div>
      <!-- Email List Ends -->
    </div>
    <!-- Main Body Ends -->

    <div class="box_slae" id="box1">
    <div id="deletebtn" onclick="dltFun();"><i class="fas fa-times"></i></div>
    <div class='popup-window new-mail'>
        <div class='header'>
            <div class='title'>New Message
            </div>            
        </div>
        <div class='min-hide'>
                <input class='receiver input-large' type='text' placeholder='Recipients' value=''/>
                <input class='input-large' type='text' placeholder='Subject'/>
            </div>
        <textarea class='min-hide_textera' rows="6" placeholder='Message'></textarea>
        <div class='menu min-hide'>
            <button class='button-large button-blue'>Send</button>
            <div class="file-input">
                <input
                    type="file"
                    name="file-input"
                    id="file-input"
                    class="file-input__input"
                />
                <label class="file-input__label" for="file-input">
                    <span><i class='fa fa-paperclip'></i></span></label
                >
                </div>
                <div class='trash_btn'>
                    <button class='button-large button-silver'><i class='fa fa-trash'></i></button>
                </div>
            </div>
        </div>
    </div>

            
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="mail_details">
                    @include('user.mail.model_body')
                </div>
                <div class="modal-footer">
                    <button type="button" class="print_btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('mail.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query
                    },
                    success: function(data) {
                        $('tbody').html(data.data);
                    }
                });
            }

            $(document).on('keyup', '#search', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                fetch_data(page, sort_type, column_name, query);
            });

            $(document).on('click', '.sorting', function() {
                var column_name = $(this).data('column_name');
                var order_type = $(this).data('sorting_type');
                var reverse_order = '';
                if (order_type == 'asc') {
                    $(this).data('sorting_type', 'desc');
                    reverse_order = 'desc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<i class="fa fa-arrow-down"></i>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<i class="fa fa-arrow-up"></i>');
                }
                $('#hidden_column_name').val(column_name);
                $('#hidden_sort_type').val(reverse_order);
                var page = $('#hidden_page').val();
                var query = $('#search').val();
                fetch_data(page, reverse_order, column_name, query);
            });

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                $('#hidden_page').val(page);
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();

                var query = $('#search').val();

                $('li').removeClass('active');
                $(this).parent().addClass('active');
                fetch_data(page, sort_type, column_name, query);
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on("click", '.view_details', function() {
                var id = $(this).data('id');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('mail.view') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                    },
                    success: function(response) {
                        $('#mail_details').html(response.view);
                        $("#emailModal").modal('show');
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('Error fetching mail details: ' + textStatus);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To remove this mail",
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
