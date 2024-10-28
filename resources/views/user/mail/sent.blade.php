@extends('user.layouts.master')
@section('title')
Sent Mail - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
@php
use App\Helpers\Helper;
@endphp
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
                        <input type="checkbox" />
                        <span class="material-symbols-outlined"> arrow_drop_down </span>
                        <span class="material-symbols-outlined"> redo </span>
                        <span class="material-symbols-outlined"> delete </span>
                    </div>
                    <div class="emailList__settingsRight">
                        <span class="material-symbols-outlined"> chevron_left </span>
                        <span class="material-symbols-outlined"> chevron_right </span>
                    </div>
                </div>
                <!-- Settings Ends -->

                <!-- Section Starts -->
                <div class="emailList__sections">



                </div>
                <!-- Section Ends -->

                <!-- Email List rows starts -->
                <div class="emailList__list">
                    <!-- Email Row Starts -->
                    @if ($mails->count() > 0)
                    @foreach ($mails as $mail)

                    <div class="emailRow ">
                        <div class="emailRow__options">
                            <input type="checkbox" name="" id="" />
                            <span class="material-symbols-outlined"> star_border </span>
                        </div>

                        <h3 class="emailRow__title view-mail"
                            data-route="{{ route('mail.sent.view', base64_encode($mail->id)) }}">{{
                            $mail->user->full_name ?? '' }}</h3>

                        <div class="emailRow__message view-mail"
                            data-route="{{ route('mail.sent.view', base64_encode($mail->id)) }}">
                            <h4>
                                {{ $mail->subject }}
                                <span class="emailRow__description view-mail"
                                    data-route="{{ route('mail.sent.view', base64_encode($mail->id)) }}"> - {!!
                                    $mail->message !!} </span>
                            </h4>
                        </div>

                        <p class="emailRow__time view-mail"
                            data-route="{{ route('mail.sent.view', base64_encode($mail->id)) }}">{{
                            $mail->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                    @else
                    <div class="mt-3 text-center">
                        <h3 class="emailRow__title">No mail found</h3>
                    </div>
                    @endif

                    <!-- Email Row Ends -->
                </div>
                <!-- Email List rows Ends -->
            </div>
            <!-- Email List Ends -->
        </div>
        <!-- Main Body Ends -->

        @include('user.mail.partials.create-mail')


    </div>
</div>

@endsection

@push('scripts')
<script>
    //view mail
        $(document).on('click', '.view-mail', function(e) {
            e.preventDefault();
            var route = $(this).data('route');
            window.location.href = route;
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