@php
    use App\Helpers\Helper;
@endphp
@if (isset($is_group_info))
    <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content group_create">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalToggleLabel2">Add Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('team-chats.add-member-team') }}" method="post" enctype="multipart/form-data"
                    id="add-member-team">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team['id'] }}">
                    <div class="modal-body">
                        <div class="member_add">

                            <div class="search-field float-right">
                                <input type="text" name="search" id="member-search" placeholder="search..."
                                    class="form-control">
                                <button class="submit_search" id="search-button"> <span class=""><i
                                            class="fa fa-search"></i></span></button>
                            </div>

                            <ul id="add-member">
                                @if (count($members) > 0)
                                    @foreach ($members as $user)
                                        <li class="member-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $user->id }}"
                                                    {{ Helper::checkMemberInTeam($team['id'], $user->id) == true ? 'disabled' : '' }}
                                                    id="flexCheckDefault" name="members[]">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar">
                                                <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                                    alt="">
                                            </div>
                                            <div>
                                                <p class="GroupName">{{ $user->full_name }}</p>
                                                <p class="GroupDescrp">{{ $user->email }}</p>
                                            </div>

                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                        <button type="button" class="btn btn-dark back-to-group-info-one"
                            data-team-id="{{ $team['id'] }}">Back </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="groupInfo" aria-hidden="true" aria-labelledby="groupInfo1" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content group_create">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupInfo1">Group Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="group_crate">
                        <div class="group_text_right">
                            @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true)
                                <div class="dropdown">
                                    <button class="group_text_right" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item edit-name-des " data-team-id="{{ $team['id'] }}"
                                                href="javascript:void(0);"><i class="fa-solid fa-pen"></i> Edit
                                                Name & Description</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalToggle2" href="javascript:void(0);"><i
                                                    class="fa-solid fa-plus"></i> Add
                                                Member</a></li>
                                    </ul>
                                </div>
                            @endif

                        </div>
                        <div class="mb-3">
                            <div class="image-upload">
                                <div class="image-wrap team-image-{{ $team['id'] }}"><img id="blah"
                                        src="{{ $team->group_image ? Storage::url($team->group_image) : asset('user_assets/images/group.jpg') }}" />
                                </div>
                                @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true)
                                    <input class="btn-inputfile team-profile-picture" id="new" type="file"
                                        name="file" data-team-id="{{ $team['id'] }}" onchange="readURL(this);" />
                                    <label for="new"><i class="fa-solid fa-camera"></i></label>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <h4 class="group-name-{{ $team['id'] }}">{{ $team->name }}</h4>
                        </div>
                        <div class="mb-3">
                            <p class="group-des-{{ $team['id'] }}">{{ $team->description }}</p>
                        </div>
                    </div>
                    <div class="min_height400">
                        <ul>
                            @if (isset($team->members) && count($team->members) > 0)
                                @foreach ($team->members as $member)
                                    <li class="group" id="group-member-{{ $team->id }}-{{ $member->user_id }}">
                                        <div class="avatar"><img
                                                src="{{ $member->user && $member->user->profile_picture
                                                    ? Storage::url($member->user->profile_picture)
                                                    : asset('user_assets/images/profile_dummy.png') }}"
                                                alt=""></div>
                                        <p class="GroupName">{{ $member->user ? $member->user->full_name : '' }}</p>
                                        <p class="GroupDescrp">{{ $member->user ? $member->user->email : '' }}</p>
                                        <div class="time_online mt-3" id="show-permission-{{ $team->id }}-{{ $member->user_id }}">
                                            @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true && $member->is_admin == 0)
                                                <div class="dropdown">
                                                    <button class="eleplish dropdown-toggle" type="button"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item make-admin"
                                                            data-team-id="{{ $team['id'] }}"
                                                            data-user-id="{{ $member->user_id }}" href="#">
                                                            <i class="fa-solid fa-shield" style="
                                                            color: #643271;
                                                        "></i> Make Admin</a></li>
                                                        <li><a class="dropdown-item remove-member-from-group"
                                                                data-bs-toggle="modal"
                                                                data-team-id="{{ $team['id'] }}"
                                                                data-user-id="{{ $member->user_id }}" href="#">
                                                                <i class="fa-solid fa-trash" style="
                                                                color: #643271;
                                                            "></i> Remove</a></li>

                                                    </ul>
                                                </div>
                                            @else
                                                @if ($member->is_admin == 1)
                                                    <span class="admin_name">Admin</span>
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <div class="alert alert-danger" role="alert">
                                    No data found!
                                </div>
                            @endif

                        </ul>
                    </div>
                    @if (Helper::checkRemovedFromTeam($team->id, auth()->user()->id) == false)
                        <div class="exit_group">
                            <a href="javascript:void(0);" data-team-id="{{ $team['id'] }}"
                                class="exit-from-group"><i class="fa-solid fa-right-from-bracket"></i> Exit Group</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

@endif
