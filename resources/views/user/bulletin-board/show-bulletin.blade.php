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
                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">
                            @endif
                        </div>
                    </div>
                    <div class="bulletin_text">
                        <div class="bulle_left">
                            <div class="name_bull">
                                {{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}
                            </div>
                            <h4 class="">
                                {{ isset($bulletin->title) && !empty($bulletin->title) ? $bulletin->title : '' }} </h4>
                            <p class="">
                                {!! isset($bulletin->description) && !empty($bulletin->description)
                                    ? preg_replace_callback(
                                        '/(https?:\/\/[^\s]+)/',
                                        function ($m) {
                                            $url = $m[1];
                                            $short = strlen($url) > 40 ? substr($url, 0, 40) . '...' : $url;
                                            return '<a href="' .
                                                $url .
                                                '" target="_blank" style="color:#0d6efd; text-decoration:underline;">' .
                                                $short .
                                                '</a>';
                                        },
                                        nl2br(e($bulletin->description)),
                                    )
                                    : '' !!}

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
                            <div class="name_bull">
                                {{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}
                            </div>
                            <h4 class="">
                                {{ isset($bulletin->title) && !empty($bulletin->title) ? $bulletin->title : '' }} </h4>
                            <p class="">
                                {!! isset($bulletin->description) && !empty($bulletin->description)
                                    ? preg_replace_callback(
                                        '/(https?:\/\/[^\s]+)/',
                                        function ($m) {
                                            $url = $m[1];
                                            $short = strlen($url) > 40 ? substr($url, 0, 40) . '...' : $url;
                                            return '<a href="' .
                                                $url .
                                                '" target="_blank" style="color:#0d6efd; text-decoration:underline;">' .
                                                $short .
                                                '</a>';
                                        },
                                        nl2br(e($bulletin->description)),
                                    )
                                    : '' !!}

                            </p>
                        </div>
                        <div class="time_bulle">{{ date('d M, Y h:i A', strtotime($bulletin->created_at)) }}</div>
                    </div>
                    <div class="bulletin_img_name">
                        <div class="main_avtar">
                            @if (isset($bulletin->user->profile_picture) && !empty($bulletin->user->profile_picture))
                                <img src="{{ Storage::url($bulletin->user->profile_picture) }}" alt="">
                            @else
                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">
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
