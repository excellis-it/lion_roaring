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
            <input type="checkbox" />
            <span class="material-icons"> arrow_drop_down </span>
            <span class="material-icons"> redo </span>
            <span class="material-icons"> delete </span>
          </div>
          <div class="emailList__settingsRight">
            <span class="material-icons"> chevron_left </span>
            <span class="material-icons"> chevron_right </span>
            <span class="material-icons"> settings </span>
          </div>
        </div>
        <!-- Settings Ends -->

        <!-- Section Starts -->
        <div class="emailList__sections">
          <div class="section section__selected">
            <span class="material-icons"> inbox </span>
            <h4>Primary</h4>
          </div>

          <!-- <div class="section">
            <span class="material-icons"> people </span>
            <h4>Social</h4>
          </div>

          <div class="section">
            <span class="material-icons"> local_offer </span>
            <h4>Promotions</h4>
          </div> -->


        </div>
        <!-- Section Ends -->

        <!-- Email List rows starts -->
        <div class="emailList__list">
          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber<span class="emailRow__description">
                  - on Your Channel Future Coders
                </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">Google</h3>

            <div class="emailRow__message">
              <h4>
                Login on New Device<span class="emailRow__description">
                  - is this your Device ?
                </span>
              </h4>
            </div>

            <p class="emailRow__time">2am</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber<span class="emailRow__description">
                  - on Your Channel Future Coders
                </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">Google</h3>

            <div class="emailRow__message">
              <h4>
                Login on New Device<span class="emailRow__description">
                  - is this your Device ?
                </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber<span class="emailRow__description">
                  - on Your Channel Future Coders
                </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">Google</h3>

            <div class="emailRow__message">
              <h4>
                Login on New Device<span class="emailRow__description">
                  - is this your Device ?
                </span>
              </h4>
            </div>

            <p class="emailRow__time">Mar 8</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber<span class="emailRow__description">
                  - on Your Channel Future Coders
                </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">Google</h3>

            <div class="emailRow__message">
              <h4>
                Login on New Device<span class="emailRow__description">
                  - is this your Device ?
                </span>
              </h4>
            </div>

            <p class="emailRow__time">2am</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber
                <span class="emailRow__description"> - on Your Channel Future Coders </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">YouTube</h3>

            <div class="emailRow__message">
              <h4>
                You Got a New Subscriber<span class="emailRow__description">
                  - on Your Channel Future 
                </span>
              </h4>
            </div>

            <p class="emailRow__time">10pm</p>
          </div>
          <!-- Email Row Ends -->

          <!-- Email Row Starts -->
          <div class="emailRow">
            <div class="emailRow__options">
              <input type="checkbox" name="" id="" />
              <span class="material-icons"> star_border </span>
              <span class="material-icons"> label_important </span>
            </div>

            <h3 class="emailRow__title">Google</h3>

            <div class="emailRow__message">
              <h4>
                Login on New Device<span class="emailRow__description">
                  - is this your Device ?
                </span>
              </h4>
            </div>

            <p class="emailRow__time">2am</p>
          </div>
          <!-- Email Row Ends -->
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
