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
            <div class="row">

                <div class="col-md-12">
                    <div class="right_content_main rounded shadow">
                        <div class="right_content">
                            <div class="my_order_titel">
                                <h4>Change Password</h4>
                            </div>
                            <div class="my_profile">
                                <div class="row">
                                    <div class="col-lg-7 mb-3">
                                        <label for="" class="form-label">Old Password :</label>
                                        <input id="password-field" type="password" class="form-control" name="password"
                                            value="">
                                        <span toggle="#password-field"
                                            class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                    <div class="col-lg-7 mb-3">
                                        <label for="" class="form-label">New Password :</label>
                                        <input id="password-field-1" type="password" class="form-control" name="password"
                                            value="">
                                        <span toggle="#password-field-1"
                                            class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                    <div class="col-lg-7 mb-3">
                                        <label for="" class="form-label">Confirm Password :</label>
                                        <input id="password-field-2" type="password" class="form-control" name="password"
                                            value="">
                                        <span toggle="#password-field-2"
                                            class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <button href="" class="add-product border-0"><span>Submit</span></button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
