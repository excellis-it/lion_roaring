@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Contact Us
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{($contact['banner_image']) ? Storage::url($contact['banner_image']) : ''}}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>{{$contact['banner_title'] ?? 'title'}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-us">
        <div class="container">
            <div class="contact-wrap-main">
                <div class="row">
                    <div class="col-xl-6 col-md-12">
                        <div class="contact-left heading_hp">
                            <h2>{{$contact['title'] ?? 'title'}}</h2>
                            <p>{{$contact['description'] ?? 'description'}}</p>
                            <form action="{{route('contact-us.form')}}" id="contact-us" method="POST">
                                @csrf
                            <div class="contact-form">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-12">
                                        <div class="form-group-wrap">
                                            <label for="First-Name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="First-Name" placeholder="" name="first_name">
                                            <span class="text-danger" id="first_name_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-12">
                                        <div class="form-group-wrap">
                                            <label for="Last-Name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="Last-Name" placeholder="" name="last_name">
                                            <span class="text-danger" id="last_name_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-12">
                                        <div class="form-group-wrap">
                                            <label for="Email-Id" class="form-label">Email Id</label>
                                            <input type="email" class="form-control" id="First-Name" placeholder="" name="email">
                                            <span class="text-danger" id="email_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-12">
                                        <div class="form-group-wrap">
                                            <label for="Phone Number" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="Last-Name" placeholder="" name="phone">
                                            <span class="text-danger" id="phone_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group-wrap">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control" id="message" rows="3" name="message"></textarea>
                                            <span class="text-danger" id="message_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 text-center">
                                        <div class="send-msg">
                                            <button type="submit" class="btn">SEND MESSAGE</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="contact-right">
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>WRITE US</h3>
                                    <p>
                                    </p>
                                    <p>{!! nl2br($contact['address']) !!}</p>
                                    <p></p>
                                </div>
                            </div>
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>CALL US</h3>
                                    <a href="tel:{{$contact['phone'] ?? 'phone'}}">{{$contact['phone'] ?? 'phone'}}</a>
                                </div>
                            </div>
                            <div class="contact-info d-flex justify-content-start align-items-start">
                                <div>
                                    <div class="con-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="con-text">
                                    <h3>EMAIL US</h3>
                                    <a href="mailto:{{$contact['email'] ?? 'email'}}">{{$contact['email'] ?? 'email'}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#contact-us').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(xhr) {
                        $('.text-danger').html('');
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                var fieldName = key.split('.')[0];
                                // Display errors for array fields
                                var num = key.match(/\d+/)[0];
                                $('#' + fieldName + '_'+num).html(value[0]);
                            } else {
                                console.log(key+'_error');
                                // after text danger span
                                $('#' + key + '_error').html(value[0]);
                            }
                        });
                    }
                });
            });
        });

    </script>
@endpush
