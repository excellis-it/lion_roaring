@extends('user.layouts.master')
@section('title')
    Dashboard - {{ env('APP_NAME') }} user profile
@endsection
@push('styles')
@endpush
@section('content')
    @php
        use App\Helpers\Helper;
    @endphp

    <div class="container-fluid">
        <div class="bg_white_border">
            <!--  Row 1 -->

            <div class="row">
                <div class="col-lg-12">
                    {{-- @if (auth()->user()->hasRole('MEMBER'))
                        <div
                            class="expiery_date
                @if (isset(auth()->user()->userLastSubscription) && auth()->user()->userLastSubscription != null) @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) <= 10)
                today-expire @endif
                @endif">
                            @if (isset(auth()->user()->userLastSubscription) && auth()->user()->userLastSubscription != null)
                                @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) == 0)
                                    Today is the last day of your plan
                                @elseif (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) == 1)
                                    Tomorrow is the last day of your plan
                                @elseif (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) < 0)
                                    Expired
                                @else
                                    @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) <= 10)
                                        Your plan will expire within
                                        {{ Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) }}
                                        days
                                    @else
                                        Your plan will expire in
                                        {{ Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) }}
                                        days
                                    @endif
                                @endif
                            @else
                                <p class="text-danger">No Ongoing Plan</p>
                            @endif
                        </div>
                    @endif --}}


                   <div>


                    <div class="container">
                        <div class="row">
                            <div class="col-md-10">
                                <h3 class="mb-3 float-left">Subscription</h3>
                            </div>
                            <div class="col-md-2">
                                <!-- Additional content or buttons can go here -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="subscription-box">
                                    <div class="s-top-box">
                                        <div class="img-box">
                                            <img
                                                src="{{ isset($settings['logo']) ? Storage::url($settings['logo']) : asset('frontend_assets/images/logo.png') }}"
                                                alt="Logo" class="img-fluid">
                                        </div>
                                        <div class="sub-title-box">
                                            <p>Subscription</p>
                                            <h2>$ 9.99 <sup></sup></h2>

                                            <div class="sub-head">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur facilis earum iure ipsum porro non error, quia dicta autem quisquam!</div>
                                        </div>
                                    </div>

                                    <div class="subs-list-box">
                                        <ul>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor sit </li>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor sit amet dolor sit</li>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor sit</li>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor sit dolor</li>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor sit amet.sit amet</li>
                                            <li><span><i class="fa-solid fa-check"></i></span> Lorem ipsum dolor .</li>
                                        </ul>
                                    </div>

                                    <div class="subscribe-btn">
                                        <a href="javascript:void(0)" class="red_btn"><span>Subscribe Now</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="subscription-box">
                                    <div class="s-top-box">
                                        <div class="img-box">
                                            <img
                                                src="{{ isset($settings['logo']) ? Storage::url($settings['logo']) : asset('frontend_assets/images/logo.png') }}"
                                                alt="Logo" class="img-fluid">
                                        </div>
                                        <div class="sub-title-box">
                                            <p>Subscription</p>
                                            <h2>$ 9.99 <sup></sup></h2>

                                            <div class="sub-head">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur facilis earum iure ipsum porro non error, quia dicta autem quisquam!</div>
                                        </div>
                                    </div>

                                    <div class="subs-list-box">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="subscription-box">
                                    <div class="s-top-box">
                                        <div class="img-box">
                                            <img
                                                src="{{ isset($settings['logo']) ? Storage::url($settings['logo']) : asset('frontend_assets/images/logo.png') }}"
                                                alt="Logo" class="img-fluid">
                                        </div>
                                        <div class="sub-title-box">
                                            <p>Subscription</p>
                                            <h2>$ 9.99 <sup></sup></h2>

                                            <div class="sub-head">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur facilis earum iure ipsum porro non error, quia dicta autem quisquam!</div>
                                        </div>
                                    </div>

                                    <div class="subs-list-box">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                            <li>Lorem ipsum dolor sit amet.</li>
                                        </ul>
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
