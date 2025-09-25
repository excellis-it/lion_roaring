@extends('ecom.layouts.master')
@section('title', 'My Password')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/bn-4.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
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
          <div class="col-md-3">
            <div class="position-relative dashboard_left rounded shadow">
              <div class="d-flex align-items-center justify-content-start pb-3">
                <div class="img_image">
                  <img src="assets/images/user-1.jpg" />
                </div>
                <div class="name_price">
                  <span>Hello,</span>
                  <h5>Jhon Deo</h5>
                </div>
              </div>
              <h5 class="img-market border-top"><a href="my_orders.html"><i class="fa-solid fa-bag-shopping"></i> My Orders</a></h5>
              <h5 class="img-market border-top"><a href="#"><i class="fa-solid fa-shopping-bag"></i> My Returns</a></h5>
              <h5 class="img-market border-top"><a href="my_profile.html"><i class="fa-solid fa-user"></i> My Profile</a></h5>
              <h5 class="img-market border-top"><a href="review_rating.html"><i class="fa-solid fa-star"></i> Review & Rating</a></h5>
              <h5 class="img-market border-top"><a href="#"><i class="fa-solid fa-heart"></i> My Wishlist</a></h5>
              <h5 class="img-market border-top"><a href="change_password.html"><i class="fa-solid fa-key"></i> Change Password</a></h5>
              <h5 class="img-market border-top"><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></h5>
            </div>
          </div>
          <div class="col-md-9">
            <div class="right_content_main rounded shadow">
              <div class="right_content">
                <div class="my_order_titel">
                  <h4>Change Password</h4>
                </div>
                <div class="my_profile">
                  <div class="row">
                    <div class="col-lg-7 mb-3">
                      <label for="" class="form-label">Old Password :</label>
                      <input id="password-field" type="password" class="form-control" name="password" value="">
                          <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    <div class="col-lg-7 mb-3">
                      <label for="" class="form-label">New Password :</label>
                      <input id="password-field-1" type="password" class="form-control" name="password" value="">
                          <span toggle="#password-field-1" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    <div class="col-lg-7 mb-3">
                      <label for="" class="form-label">Confirm Password :</label>
                      <input id="password-field-2" type="password" class="form-control" name="password" value="">
                          <span toggle="#password-field-2" class="fa fa-fw fa-eye field-icon toggle-password"></span>
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
