@extends('user.layouts.master')
@section('title')
    Bulletin Board - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp">
                        <h2>Bulletin Board</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Bulletin Chat</h2>
                </div>
                <div class="bulletin_board">
                    @if (count($bulletins) > 0)
                        @foreach ($bulletins as $key => $bulletin)
                            @if ($key % 2 == 0)
                                <div class="bulletion_board_left">
                                    <div class="d-flex">
                                        <div class="bulletin_img_name">
                                            <div class="main_avtar">
                                                @if (isset($bulletin->user->profile_picture) && !empty($bulletin->user->profile_picture))
                                                    <img src="{{ Storage::url($bulletin->user->profile_picture) }}" alt="">
                                                @else
                                                    <img src="{{asset('user_assets/images/jhon.png')}}" alt="">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="bulletin_text">
                                            <div class="bulle_left">
                                                <div class="name_bull">{{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}</div>
                                                <h4 class="">
                                                    {{ isset($bulletin->title) && !empty($bulletin->title) ? $bulletin->title : '' }} </h4>
                                                <p class="">
                                                    {{ isset($bulletin->description) && !empty($bulletin->description) ? $bulletin->description : '' }}
                                                </p>
                                            </div>
                                            <div class="time_bulle">{{ date('d M, Y h:i A', strtotime($bulletin->created_at)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bulletion_board_left bulletion_board_right">
                                    <div class="d-flex">
                                        <div class="bulletin_text">
                                            <div class="bulle_left">
                                                <div class="name_bull">{{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}</div>
                                                <h4 class="">
                                                    {{ isset($bulletin->title) && !empty($bulletin->title) ? $bulletin->title : '' }} </h4>
                                                <p class="">
                                                    {{ isset($bulletin->description) && !empty($bulletin->description) ? $bulletin->description : '' }}
                                                </p>
                                            </div>
                                            <div class="time_bulle">{{ date('d M, Y h:i A', strtotime($bulletin->created_at)) }}</div>
                                        </div>
                                        <div class="bulletin_img_name">
                                            <div class="main_avtar">
                                                @if (isset($bulletin->user->profile_picture) && !empty($bulletin->user->profile_picture))
                                                    <img src="{{ Storage::url($bulletin->user->profile_picture) }}" alt="">
                                                @else
                                                    <img src="{{asset('user_assets/images/jhon.png')}}" alt="">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center w-100">
                            <h4>No Bulletin Found</h4>
                        </div>
                    @endif
                    {{-- <div class="bulletion_board_left">
                        <div class="d-flex">
                            <div class="bulletin_img_name">
                                <div class="main_avtar"><img src="user_assets/images/jhon.png" alt=""></div>
                            </div>
                            <div class="bulletin_text">
                                <div class="bulle_left">
                                    <div class="name_bull">David Johnson</div>
                                    <h4 class="">Lorem Ipsum is simply dummy</h4>
                                    <p class="">Lorem Ipsum is simply dummy text of the printing and typesetting
                                        industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                </div>
                                <div class="time_bulle">3:22 PM</div>
                            </div>
                        </div>
                    </div>
                    <div class="bulletion_board_left">
                        <div class="d-flex">
                            <div class="bulletin_img_name">
                                <div class="main_avtar"><img src="user_assets/images/jhon.png" alt=""></div>
                            </div>
                            <div class="bulletin_text">
                                <div class="bulle_left">
                                    <div class="name_bull">David Johnson</div>
                                    <h4 class="">David Johnson</h4>
                                    <p class="">Lorem Ipsum is simply dummy text of the printing and typesetting
                                        industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                </div>
                                <div class="time_bulle">3:22 PM</div>
                            </div>
                        </div>
                    </div>
                    <div class="bulletion_board_left bulletion_board_right">
                        <div class="d-flex">
                            <div class="bulletin_text">
                                <div class="bulle_left">
                                    <div class="name_bull">David Johnson</div>
                                    <h4 class="">David Johnson</h4>
                                    <p class="">Lorem Ipsum is simply dummy text of the printing and typesetting
                                        industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                </div>
                                <div class="time_bulle">3:22 PM</div>
                            </div>
                            <div class="bulletin_img_name">
                                <div class="main_avtar"><img src="user_assets/images/jhon.png" alt=""></div>
                            </div>
                        </div>
                    </div>
                    <div class="bulletion_board_left bulletion_board_right">
                        <div class="d-flex">
                            <div class="bulletin_text">
                                <div class="bulle_left">
                                    <div class="name_bull">David Johnson</div>
                                    <h4 class="">David Johnson</h4>
                                    <p class="">Lorem Ipsum is simply dummy text of the printing and typesetting
                                        industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                </div>
                                <div class="time_bulle">3:22 PM</div>
                            </div>
                            <div class="bulletin_img_name">
                                <div class="main_avtar"><img src="user_assets/images/jhon.png" alt=""></div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
