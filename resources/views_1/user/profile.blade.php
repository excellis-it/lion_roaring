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
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-12 col-md-12">
                                <div class="d-block d-md-flex align-items-center">
                                    <div class="left_img me-3 profile_img">
                                        <span>
                                            @if (Auth::user()->profile_picture)
                                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt=""
                                                    id="blah">
                                            @else
                                                <img src="{{ asset('user_assets/images/logo.png') }}" alt=""
                                                    id="blah" />
                                            @endif
                                        </span>
                                        <div class="profile_eidd">
                                            <input type="file" id="edit_profile" onchange="readURL(this);"
                                                name="profile_picture" />
                                            <label for="edit_profile"><i class="ti ti-edit"></i></label>
                                        </div>
                                    </div>
                                    <div class="right_text profile-info">
                                        <p>Hello!</p>
                                        <h2> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                                        <p>{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row  mb-0">
                            <div class="col-lg-12">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>First Name*</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="First Name" value="{{ Auth::user()->first_name }}">
                                            @if ($errors->has('first_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('first_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- middle name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                                placeholder="Middle Name" value="{{ Auth::user()->middle_name }}">
                                            @if ($errors->has('middle_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('middle_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- last name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Last Name*</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder="Last Name" value="{{ Auth::user()->last_name }}">
                                            @if ($errors->has('last_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('last_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- phone number --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Phone Number*</label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                                placeholder="Phone Number" value="{{ Auth::user()->phone }}">
                                            @if ($errors->has('phone_number'))
                                                <div class="error" style="color:red;">{{ $errors->first('phone_number') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- address --}}
                                    <div class="col-md-8 mb-2">
                                        <div class="box_label">
                                            <label>Address*</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                placeholder="Address" value="{{ Auth::user()->address }}">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red;">{{ $errors->first('address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                        <button class="print_btn" type="submit">Update</button>
                                    </div>

                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
