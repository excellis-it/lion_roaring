@extends('ecom.layouts.master')
@section('title', 'My Password')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('change-password') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>My Password</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard_section section change-ps">
        <div class="container">

            {{-- Flash + validation messages --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="right_content_main rounded shadow">
                        <div class="right_content">
                            <div class="my_order_titel">
                                <h4>Change Password</h4>
                            </div>
                            <div class="my_profile">

                                <form method="POST" action="{{ route('e-store.password.update') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label">Old Password :</label>
                                            <input id="password-field" type="password" class="form-control"
                                                name="current_password" required>
                                            <span toggle="#password-field"
                                                class="fa fa-fw fa-eye field-icon toggle-password"
                                                style="cursor:pointer;"></span>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label">New Password :</label>
                                            <input id="password-field-1" type="password" class="form-control"
                                                name="password" required minlength="8" autocomplete="new-password">
                                            <span toggle="#password-field-1"
                                                class="fa fa-fw fa-eye field-icon toggle-password"
                                                style="cursor:pointer;"></span>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label">Confirm Password :</label>
                                            <input id="password-field-2" type="password" class="form-control"
                                                name="password_confirmation" required minlength="8"
                                                autocomplete="new-password">
                                            <span toggle="#password-field-2"
                                                class="fa fa-fw fa-eye field-icon toggle-password"
                                                style="cursor:pointer;"></span>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <button type="submit" class="red_btn add-product border-0"><span>Submit</span></button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.querySelectorAll('.toggle-password').forEach(function(el) {
                el.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('toggle'));
                    if (!target) return;
                    target.type = target.type === 'password' ? 'text' : 'password';
                    this.classList.toggle('fa-eye-slash');
                });
            });
        </script>
    @endpush
@endsection
