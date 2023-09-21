@extends('admin.layouts.master')
@section('title')
    Dashboard - Demo admin
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
            <ul class="nav" role="tablist" id="myTab">
                <li role="presentation">
                    <a href="index.html" class="active"><i class="ph-airplane-tilt"></i> Flight</a>
                </li>
                <li role="presentation"><a href=""><i class="ph-buildings"></i> Hotel</a></li>
            </ul>
            <div class="">
                <!-- <div class="top_4">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card card_one">
                                <div class="p-3">
                                    <div class="card-icon card-icon-large"></div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Overall Bookings</h5>
                                        <span class="month_year">
                                            <a href="">M</a>
                                            <a href="">Y</a>
                                        </span>
                                    </div>
                                    <div class="booking_text">
                                        <small>Total Bookings</small>
                                        <h3 class=""><i class="ph-arrow-up ph-arrow-up-green"></i><span class="timer count-title count-number" data-to="7128" data-speed="3000">7128</span></h3>
                                    </div>
                                    <div class="progress_bar">
                                        <span>85%</span>
                                        <div class="progress mt-1" data-height="8" style="height: 8px;">
                                            <div class="progress-bar l-bg-purple" role="progressbar" data-width="75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card card_two">
                                <div class="p-3">
                                    <div class="card-icon card-icon-large"></div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Overall Partners</h5>
                                        <span class="month_year">
                                            <a href="">M</a>
                                            <a href="">Y</a>
                                        </span>
                                    </div>
                                    <div class="booking_text">
                                        <small>Total Partners</small>
                                        <h3 class=""><i class="ph-arrow-up ph-arrow-up-p"></i> <i class="ph-currency-inr"></i><span class="timer count-title count-number" data-to="17500" data-speed="3000">17,500</span></h3>
                                    </div>
                                    <div class="progress_bar">
                                        <span>65%</span>
                                        <div class="progress mt-1" data-height="8" style="height: 8px;">
                                            <div class="progress-bar l-bg-orange" role="progressbar" data-width="65%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card card_three">
                                <div class="p-3">
                                    <div class="card-icon card-icon-large"></div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Tax Deducation</h5>
                                        <span class="month_year">
                                            <a href="">M</a>
                                            <a href="">Y</a>
                                        </span>
                                    </div>
                                    <div class="booking_text">
                                        <small>Total Deducation</small>
                                        <h3 class="counting"><i class="ph-arrow-up ph-arrow-up-y"></i> <i class="ph-currency-inr"></i><span class="timer count-title count-number" data-to="18200" data-speed="3000">18,200</span></h3>
                                    </div>
                                    <div class="progress_bar">

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card card_four">
                                <div class="p-3">
                                    <div class="card-icon card-icon-large"></div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Revenue Stats</h5>
                                        <span class="month_year">
                                            <a href="">M</a>
                                            <a href="">Y</a>
                                        </span>
                                    </div>
                                    <div class="booking_text">
                                        <small>Total Income</small>
                                        <h3><i class="ph-arrow-up ph-arrow-up-b"></i> <i class="ph-currency-inr"></i><span class="timer count-title count-number" data-to="7128" data-speed="3000">7,128</span></h3>
                                    </div>
                                    <div class="progress_bar">
                                        <span>85%</span>
                                        <div class="progress mt-1" data-height="8" style="height: 8px;">
                                            <div class="progress-bar bg-blue-grey" role="progressbar" data-width="75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
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
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-12">
                            <div class="bar_chart card p-3">
                                <h5 class="card-title mb-0">Last 6 month Monthwise booking
                                </h5>
                                <!-- <div class="d-block d-sm-flex total_booking">
                                    <h5>3564</h5>
                                    <p>Total Bookings</p>
                                    <span>18% High then Last Month</span>
                                </div> -->
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-12">
                            <div class="">
                                <h5 class="card-title mb-0">Flight Expenses / Earnings
                                </h5>
                                <div class="deposit-div-wrap d-flex">
                                    <div class="deposit-icon">
                                        <img src="img/deposit-1.svg" alt="">
                                    </div>
                                    <div class="deposit-div">
                                        <div class="deposit-text">
                                            <h3>Deposit</h3>
                                        </div>
                                        <div class="deposit-amount">
                                            <h4><span>$10.000</span>/$20.000</h4>
                                        </div>
                                        <div>
                                            <progress class="progress-bar-1" id="file" value="32"
                                                max="100"> 30% </progress>
                                        </div>
                                        <div class="progress-goal d-flex justify-content-between align-items-center">
                                            <h3>3% of your goal</h3>
                                            <h4>+3%</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="deposit-div-wrap d-flex">
                                    <div class="deposit-icon deposit-icon-2">
                                        <img src="img/deposit-2.svg" alt="">
                                    </div>
                                    <div class="deposit-div">
                                        <div class="deposit-text">
                                            <h3>Deposit</h3>
                                        </div>
                                        <div class="deposit-amount">
                                            <h4><span>$8.000</span>/$12.000</h4>
                                        </div>
                                        <div>
                                            <progress class="progress-bar-2" id="file" value="32"
                                                max="100"> 77% </progress>
                                        </div>
                                        <div class="progress-goal d-flex justify-content-between align-items-center">
                                            <h3>77% of your goal</h3>
                                            <h4>+3%</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="booking_by_sorce">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card p-3 agent_list min_height355 box_shadow">
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Top 5 Destination Booking</h5>
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
                                                    <th scope="col">Destination Name</th>
                                                    <th scope="col">Bookings</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td scope="row">1</td>
                                                    <td>DEL (Delhi)</td>
                                                    <td>25</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">2</td>
                                                    <td>CCU (Kolkata)</td>
                                                    <td>15</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">3</td>
                                                    <td>BLR (Bangalore)</td>
                                                    <td>20</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">4</td>
                                                    <td>MAA (Chennai)</td>
                                                    <td>21</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">5</td>
                                                    <td>HYD (Hyderabad)</td>
                                                    <td>15</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card p-3 agent_list min_height355 box_shadow">
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="card-title mb-0">Top 5 Airline Booking
                                        </h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Airlines</th>
                                                    <th scope="col">Bookings</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td scope="row">1</td>
                                                    <td>Vistara</td>
                                                    <td>25</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">2</td>
                                                    <td>Air India Express</td>
                                                    <td>15</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">3</td>
                                                    <td>Indigo</td>
                                                    <td>20</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">4</td>
                                                    <td>Air Asia</td>
                                                    <td>21</td>
                                                </tr>
                                                <tr>
                                                    <td scope="row">5</td>
                                                    <td>SpiceJet</td>
                                                    <td>15</td>
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
@endsection

@push('scripts')
@endpush
