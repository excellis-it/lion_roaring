@php
    use App\Helpers\Helper;
@endphp
@if (isset($is_group_info))

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
                                        <li><a class="dropdown-item edit-name-des " data-team-id="{{$team['id']}}" href="#"><i class="fa-solid fa-pen"></i> Edit
                                                Name & Description</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" href="#"><i class="fa-solid fa-plus"></i> Add
                                                Member</a></li>
                                    </ul>
                                </div>
                            @endif

                        </div>
                        <div class="mb-3">
                            <div class="image-upload">
                                <div class="image-wrap team-image-{{$team['id']}}"><img id="blah"
                                        src="{{ $team->group_image ? Storage::url($team->group_image) : asset('user_assets/images/group.jpg') }}" />
                                </div>
                                @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true)
                                    <input class="btn-inputfile team-profile-picture" id="new" type="file" name="file" data-team-id="{{$team['id']}}" onchange="readURL(this);"/>
                                    <label for="new"><i class="fa-solid fa-camera"></i></label>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <h4 class="group-name-{{$team['id']}}">{{ $team->name }}</h4>
                        </div>
                        <div class="mb-3">
                            <p class="group-des-{{$team['id']}}">{{ $team->description }}</p>
                        </div>
                    </div>
                    <div class="min_height400">
                        <ul>
                            @if (isset($team->members) && count($team->members) > 0)
                                @foreach ($team->members as $member)
                                    <li class="group" id="group-member-{{$team->id}}-{{$member->user_id}}">
                                        <div class="avatar"><img
                                                src="{{ $member->user->profile_picture ? Storage::url($member->user->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                                alt=""></div>
                                        <p class="GroupName">{{ $member->user->full_name }}</p>
                                        <p class="GroupDescrp">{{ $member->user->email }}</p>
                                        <div class="time_online">
                                            @if (Helper::checkAdminTeam(auth()->user()->id, $team->id) == true  && $member->is_admin == 0)
                                                <div class="dropdown">
                                                    <button class="eleplish dropdown-toggle" type="button"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item remove-member-from-group" data-bs-toggle="modal" data-team-id="{{$team['id']}}" data-user-id="{{$member->user_id}}"
                                                                href="#"> <i class="fa-solid fa-trash"></i> Remove</a></li>
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

                        </ul>
                    </div>
                    <div class="exit_group">
                        <a href=""><i class="fa-solid fa-right-from-bracket"></i> Exit Group</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif
