@extends('admin.layouts.master')
@section('title')
    Dashboard - {{env('APP_NAME')}} admin
@endsection
@push('styles')
@endpush
@section('head')
    Dashboard
@endsection
@section('content')
    <div class="main-content" style="min-height: 842px;">

        <div class="dashboard_tab pt-5 pl-0 pb-5 pl-sm-5">
            <!-- Nav tabs -->

            <div class="">

                <div class="left_right">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <h2 class="flight_titel">Flight</h2> -->
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_1">
                                        <h3>0</h3>
                                        <p>Today Confirmed Booking</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_2">
                                        <h3>49</h3>
                                        <p>This Month Booking</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_3">
                                        <h3>1063</h3>
                                        <p>Total Booking</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_4">
                                        <h3>0</h3>
                                        <p>Today Pending Booking</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_1">
                                        <h3>0</h3>
                                        <p>New Deposit Request</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_2">
                                        <h3>9</h3>
                                        <p>New Agent Request</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_3">
                                        <h3>76</h3>
                                        <p>Total Agents</p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="mini_box small_bg_4">
                                        <h3>697,563</h3>
                                        <p>All Agent Balance</p>
                                    </div>
                                </div>
                            </div>
                            <div class="booking_by_sorce">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card p-3 min_height355 box_shadow">
                                            <div class="mb-2 d-flex justify-content-between">
                                                <h5 class="card-title mb-0">Booking by Source</h5>
                                                <!-- <span class="month_year">
                                                    <a href="">M</a>
                                                    <a href="">Y</a>
                                                </span> -->
                                            </div>
                                            <div
                                                class="total-booking-div d-flex justify-content-between align-items-center">
                                                <div class="">
                                                    <div class="text-center count_text d-flex">
                                                        <span class="round_color_p"></span>
                                                        <p>Agency <br><b>2500</b></p>
                                                    </div>
                                                    <div class="text-center count_text d-flex">
                                                        <span class="round_color_b"></span>
                                                        <p>Corporates <br><b>3630</b></p>
                                                    </div>
                                                    <div class="text-center count_text  d-flex">
                                                        <span class="round_color_lb"></span>
                                                        <p>Others <br><b>4870</b></p>
                                                    </div>
                                                </div>
                                                <div id="donut-example" class="morris-donut-inverse"></div>
                                                <!-- <div class="d-flex justify-content-around">
                                                <div class="text-center count_text">
                                                    <span class="round_color_p"></span>
                                                    <p>Agency <br><b>2500</b></p>
                                                </div>
                                                <div class="text-center count_text">
                                                    <span class="round_color_b"></span>
                                                    <p>Corporates <br><b>3630</b></p>
                                                </div>
                                                <div class="text-center count_text">
                                                    <span class="round_color_lb"></span>
                                                    <p>Others <br><b>4870</b></p>
                                                </div>
                                            </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card p-3 agent_list min_height355 box_shadow">
                                            <div class="mb-2 d-flex justify-content-between">
                                                <h5 class="card-title mb-0">Top Agents List</h5>
                                                <!-- <span class="month_year">
                                                    <a href="">M</a>
                                                    <a href="">Y</a>
                                                </span> -->
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Agent Name</th>
                                                            <th scope="col">Bookings</th>
                                                            <th scope="col">Total Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td scope="row">1</td>
                                                            <td>Flight Mantra</td>
                                                            <td>452</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">2</td>
                                                            <td>SG Travels</td>
                                                            <td>156</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">3</td>
                                                            <td>Fly Trip</td>
                                                            <td>58</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">4</td>
                                                            <td>KOGENT CONNECT</td>
                                                            <td>85</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">5</td>
                                                            <td>Goibibo</td>
                                                            <td>456</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">6</td>
                                                            <td>Make My Trip</td>
                                                            <td>21</td>
                                                            <td>₹ 50452</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>



        </div>
    </div>
@endsection

@push('scripts')
@endpush
