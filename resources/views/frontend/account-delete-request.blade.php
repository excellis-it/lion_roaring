@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Account Delete Request
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>Account Delete Request</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="login-sec">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-10 col-lg-12 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp">
                        {{-- Account Delete Request static content for play store and app store submission policy  --}}
                        <div class="account-delete-content">
                            <h2>Requesting Account Deletion</h2>
                            <p>
                                At <strong>{{ env('APP_NAME') }}</strong>, we respect your privacy and give you full control
                                over your data.
                                If you decide to delete your account, please submit a request using the contact form
                                provided at the bottom of this page.
                            </p>

                            <h3>How to Submit Your Request:</h3>
                            <ol>
                                <li>Scroll down to the contact form.</li>
                                <li>Enter the email address associated with your account and any additional details that
                                    will help us verify your identity.</li>
                                <li>Click <strong>Submit</strong> to send your request.</li>
                            </ol>

                            <h3>What Happens After Submission?</h3>
                            <ul>
                                <li>Our support team will review and verify your request.</li>
                                <li>Your account and all associated personal data will be permanently deleted after
                                    verification.</li>
                                <li>This action is <em>irreversible</em>; once your data is removed, it cannot be recovered.
                                </li>
                            </ul>

                            <p>
                                For more information, please see our&nbsp;
                                <a href="/privacy-policy" target="_blank">Privacy&nbsp;Policy</a>.
                            </p>
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </section>
@endsection

@push('scripts')
@endpush
