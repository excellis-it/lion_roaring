@extends('ecom.layouts.master')
@section('title', 'Contact Us')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/slider-bg.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2>Contact Us</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="form-sec common-padd">
        <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="contact-card-wprepper">
                            <div class="con-card text-center">
                                <div class="icon"><i class="fa-regular fa-envelope"></i></div>
                                <div class="content">
                                    <div class="icon-heading">Mail us</div>
                                    <div class="cont-details">
                                        <a href=""> info@company.gmail.com</a>
                                    </div>
                                </div>
                            </div>

                            <div class="con-card text-center">
                                <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="content">
                                    <div class="icon-heading">Follow us</div>
                                    <div class="cont-details">
                                        <a href=""> Lorem ipsum dolor sit amet consectetur </a>
                                    </div>
                                </div>
                            </div>

                            <div class="con-card text-center">
                                <div class="icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="content">
                                <div class="icon-heading">Call us</div>
                                <div class="cont-details">
                                    <a href=""> +91 8789 678 954</a>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-area-wrepper">
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                              <label for="" class="form-label">First Name: </label>
                              <input type="text" class="form-control" aria-label="First name" placeholder="First Name">
                            </div>
                            <div class="col-lg-6 mb-4">
                              <label for="" class="form-label">Last Name: </label>
                              <input type="text" class="form-control" aria-label="Last name" placeholder="Last Name">
                            </div>
                            <div class="col-lg-6 mb-4">
                                <label for="" class="form-label">Email: </label>
                                <input type="email" class="form-control" aria-label="email" placeholder="Email">
                              </div>
                              <div class="col-lg-6 mb-4">
                                <label for="" class="form-label">Phnone Number: </label>
                                <input type="tel" class="form-control" aria-label="Phone" placeholder="Phnone Number">
                              </div>
                              <div class="col-lg-12 mb-4">
                                <label for="" class="form-label">Type Your Massege: </label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"  placeholder="Type Your Massege..."></textarea>
                              </div>
                              <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                              </div>
                          </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="common-padd pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="call-rivan">
                        <div class="item">
                            <div class="img-box"><img src="{{ asset('ecom_assets/images/slider-bg.png') }}" alt=""></div>
                            <div class="text-box">
                                <h2>Sales manager</h2>
                                <p>Mr. Johan</p>
                            </div>
                        </div>
                        <div class="item">
                            <h2>Call us Today! We’ll be happy to assist you.</h2>
                        </div>
                        <div class="item">
                            <a href="" class="call-now-btn"><span>Call Now</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="follow-us common-padd pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>follow Us On</h2>
                    <div class="follow-us-icon-list">
                        <ul>
                            <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                            <li><a href="#"><i class="fa-brands fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d52859625.8737515!2d-161.6458215068694!3d36.03749288732443!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sin!4v1727157639648!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>


@endsection
