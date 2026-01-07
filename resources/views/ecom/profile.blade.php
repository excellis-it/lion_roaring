@extends('ecom.layouts.master')
@section('title', 'My Profile')

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ \App\Helpers\Helper::estorePageBannerUrl('profile') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
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
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <div class="user-profile-box shadow">
                        <div class="user-img">
                            {{-- Avatar or placeholder --}}
                            @php use Illuminate\Support\Facades\Storage; @endphp
                            @if (!empty($user->profile_picture))
                                <img src="{{ Storage::url($user->profile_picture) }}" alt="User">
                            @else
                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="User">
                            @endif

                            <div class="social-box">
                                <ul>
                                    <li>
                                        <!-- Profile picture upload (auto-submit on change) -->
                                        <form id="avatar-form" action="{{ route('e-store.update-profile') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" id="upload" name="profile_picture" accept="image/*"
                                                hidden onchange="this.form.submit()">
                                            <label for="upload" class="upload-btn" title="Upload profile picture">
                                                <i class="fa-solid fa-upload"></i>
                                            </label>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="profile-details">
                            <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p>{{ $user->user_name ?? '' }}</p>
                        </div>
                        <div class="others-details">
                            <ul>
                                <li>
                                    <a href="{{ route('e-store.my-orders') }}">
                                        <span class="identy-box"><i class="fa-solid fa-bag-shopping"></i></span> My Orders
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('e-store.wishlist') }}">
                                        <span class="identy-box"><i class="fa-solid fa-heart"></i></span> Wish List
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('e-store.cart') }}">
                                        <span class="identy-box"><i class="fa-solid fa-cart-shopping"></i></span> My Cart
                                    </a>
                                </li>
                                <li>
                                    <a href="tel:{{ $user->phone }}">
                                        <span class="identy-box"><i class="fa-solid fa-phone"></i></span>
                                        {{ $user->phone ?? '-' }}
                                    </a>
                                </li>
                            </ul>
                            <hr>
                        </div>
                        <div class="others-details">
                            <ul>
                                <li>
                                    <a href="{{ route('e-store.change-password') }}">
                                        <span class="identy-box"><i class="fa-solid fa-lock"></i></span>
                                        Change Password
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}">
                                        <span class="identy-box"><i class="fa-solid fa-right-from-bracket"></i></span>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="other-information shadow">
                        <div class="edit-profile">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#editProfileModal"><i
                                    class="fa-solid fa-pencil"></i></a>
                        </div>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Name :</td>
                                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                </tr>
                                {{-- <tr>
                                    <td>Date of birth :</td>
                                    <td>{{ $user->date_of_birth ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Gender :</td>
                                    <td>{{ $user->gender ?? '-' }}</td>
                                </tr> --}}

                                <tr>
                                    <td>Email :</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone number :</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Delivery Address :</td>
                                    <td>{{ $user->defaultDeliveryAddress?->formatted_address ?? '-' }}
                                        &nbsp;
                                        <a href="javascript:void(0)" onclick="changeLocation()" style="cursor: pointer;">
                                            <span><i class="fa fa-pencil"></i></span>
                                        </a>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Profile Modal --}}
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('e-store.update-profile') }}" class="modal-content"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="{{ old('first_name', $user->first_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="{{ old('last_name', $user->last_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-12" hidden>
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address', $user->address) }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
