@extends('ecom.layouts.master')
@section('title', 'My Profile')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/bn-4.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>My Profile</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="profile-sec common-padd">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="user-profile-box shadow">
                    <div class="user-img">
                      <img src="{{ asset('ecom_assets/images/bn-5.jpg') }}" alt="User">
                    
                      <div class="social-box">
                        <ul>
                          <li>
                            <!-- Hidden file input -->
                            <input type="file" id="upload" hidden>
                    
                            <!-- Label acts as button -->
                            <label for="upload" class="upload-btn">
                              <i class="fa-solid fa-upload"></i>
                            </label>
                          </li>
                        </ul>
                      </div>
                    </div>

                    <div class="profile-details">
                        <h3>Julian Swan</h3>
                        <p>Lion Roaring</p>
                    </div>
                    <div class="others-details">
                        <ul>
                            <li><a href="#"><span class="identy-box"><i class="fa-solid fa-bag-shopping"></i></span> My Orders</a></li>
                            <li><a href="#"><span class="identy-box"><i class="fa-solid fa-link"></i></span> Quick links</a></li>
                            <li><a href="#"><span class="identy-box"><i class="fa-solid fa-cart-shopping"></i></span> My Cart</a></li>
                            <li><a href="#"><span class="identy-box"><i class="fa-solid fa-phone"></i></span> 5467-765-567</a></li>
                        </ul>
                        <hr>
                    </div>
                    <div class="others-details">
                      <ul>
                          <li><a href="#"><span class="identy-box"><i class="fa-solid fa-address-card"></i></span> Trust And Verification</a></li>
                          <li><a href="#"><span class="identy-box"><i class="fa-solid fa-lock"></i></span> Change Password</a></li>
                          <li><a href="#"><span class="identy-box"><i class="fa-solid fa-envelope"></i></span> Change email address</a></li>
                      </ul>
                  </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="other-information shadow">
                  <div class="edit-profile">
                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                  </div>
                    <table class="table">
                      <tbody>
                        <tr>
                          <td>Name : </td>
                          <td>Jordan</td>
                        </tr>
                        <tr>
                          <td>Date of birth : </td>
                          <td>05-07-2000</td>
                        </tr>
                        <tr>
                          <td>Gender :</td>
                          <td>Male</td>
                        </tr>
                        <tr>
                          <td>Home Address :</td>
                          <td>Dubai</td>
                        </tr>
                        <tr>
                          <td>Email :</td>
                          <td>@twittergmail.com</td>
                        </tr>
                        <tr>
                          <td>Phone number :</td>
                          <td>2345-5432-56</td>
                        </tr>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </section>


@endsection
