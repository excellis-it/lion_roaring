@extends('ecom.layouts.master')
@section('title', ' Tracking')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('ecom_assets/images/bn-4.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-8 col-md-12">
                    <div class="inner_banner_ontent">
                        <h2> Tracking</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    <section class="common-padd">
        <div class="container">
            <div class="heading_hp pe-0 pe-lg-5">
                <h2 class="text-white">Track you order</h2>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="order-id-box">
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter your order id">
                            </div>
                            <button type="submit" class="w-100 red_btn border-0"><span>Track Order <i class="fa-solid fa-arrow-right"></i></span></button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="delevry-sumry">
                        <div class="info-del">
                            <h4>Info about your order</h4>
                            <p>#6465</p>
                        </div>
                        <div class="info-del">
                            <p>2yp Status : <span> Delivered </span></p>
                            <h4>The ordered has be delivered successfully</h4>
                            
                        </div>
                        
                        
                        <div class="d-position">
                        <ul>
                            <li>
                                <span class="btn btn-primary"><i class="fa-solid fa-check"></i></span>
                                <p>Ordered</p>
                            </li>
                             <li>
                                <span class="btn btn-warning"><i class="fa-solid fa-check"></i></span>
                                <p>In Trasit</p>
                            </li>
                             <li>
                                <span class="btn btn-secondary"><i class="fa-solid fa-check"></i></span>
                                <p>Out for Delevery</p>
                            </li>
                             <li>
                                <span class="btn btn-success"><i class="fa-solid fa-check"></i></span>
                                <p>Delivered</p>
                            </li>
                        </ul>
                    </div>
                    
                    
                    
                    <div class="d-details mt-5">
                        <h5>Delivered <i class="fa-solid fa-arrow-right"></i> <span>Delivered In/At Mailbox</span></h5>
                        <p>2025-11-24 <span>10:35 UTC</span></p>
                    </div>
                    <div class="d-details mt-3">
                        <h5>Delivered <i class="fa-solid fa-arrow-right"></i> <span>Delivered In/At Mailbox</span></h5>
                        <p>2025-11-24 <span>10:35 UTC</span></p>
                    </div>
                    <div class="d-details mt-3">
                        <h5>Delivered <i class="fa-solid fa-arrow-right"></i> <span>Delivered In/At Mailbox</span></h5>
                        <p>2025-11-24 <span>10:35 UTC</span></p>
                    </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </section>


@endsection
