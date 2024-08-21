@php
    use App\Helpers\Helper;
@endphp
@if (isset($is_group_info))

<div class="modal fade" id="groupInfo" aria-hidden="true" aria-labelledby="groupInfo1" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content group_create">
            <div class="modal-header">
                <h5 class="modal-title" id="groupInfo1">Group Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="group_crate">
                    <div class="group_text_right">
                        @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true)
                        <div class="dropdown">
                            <button class="group_text_right" type="button" id="dropdownMenuButton1"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" data-bs-toggle="modal" href="#">Edit
                                        Name</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" href="#">Edit
                                        Member</a></li>
                            </ul>
                        </div>
                        @endif

                    </div>
                    <div class="mb-3">
                        <div class="image-upload">
                            <div class="image-wrap"><img id="previewImage01"
                                    src="{{ $team->group_image ? Storage::url($team->group_image) : asset('user_assets/images/group.jpg') }}" /></div>
                            <input class="btn-inputfile" id="file01" type="file" name="file" />
                            <label for="file01"><i class="fa-solid fa-camera"></i></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h4>{{ $team->name }}</h4>
                    </div>
                    <div class="mb-3">
                        <p>{{ $team->description }}</p>
                    </div>
                </div>
                <div class="min_height400">
                    <ul>
                        @if (isset($team->members) && count($team->members) > 0)
                            @foreach ($team->members as $member)
                                <li class="group">
                                    <div class="avatar"><img
                                            src="{{ $member->user->profile_picture ? Storage::url($member->user->profile_picture) : asset('user_assets/images/logo.png') }}"
                                            alt=""></div>
                                    <p class="GroupName">{{ $member->user->full_name }}</p>
                                    <p class="GroupDescrp">{{ $member->user->email }}</p>
                                    <div class="time_online">
                                        @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true)
                                        <div class="dropdown">
                                            <button class="eleplish dropdown-toggle" type="button"
                                                id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li><a class="dropdown-item" data-bs-toggle="modal"
                                                        href="#">Remove</a></li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                                @else
                                <div class="alert alert-danger" role="alert">
                                    No data found!
                                </div>
                        @endif
                        {{-- <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet</p>
                            <div class="time_online">
                                <div class="dropdown">
                                    <button class="eleplish dropdown-toggle" type="button"
                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                href="#">Remove Group</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                href="#">Add admin</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet </p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li>
                        <li class="group">
                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                    alt=""></div>
                            <p class="GroupName">David Johnson</p>
                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                        </li> --}}
                    </ul>
                </div>
                <div class="exit_group">
                    <a href=""><i class="fa-solid fa-right-from-bracket"></i> Exit Group</a>
                </div>
            </div>
            <!-- <div class="modal-footer">
                                                                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back to Create Page</button>
                                                                        <button class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                                                                      </div> -->
        </div>
    </div>
</div>
@endif

