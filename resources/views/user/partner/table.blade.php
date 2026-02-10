@if (count($partners) > 0)
    @foreach ($partners as $key => $partner)
        <tr>
            <td>
                {{ $partners->firstItem() + $key }}
            </td>
            <td>{{ $partner->email }}</td>
            <td>{{ $partner->full_name }}</td>
            <td>{{ $partner->user_type }}</td>
            <td>{{ $partner->countries->name ?? '-' }}</td>
            <td>
                <span>{{ $partner->userRole->name ?? '' }}</span>
            </td>


            <td>
                @if (isset($partner->userRole) && $partner->userRole->is_ecclesia == 0)
                {{ isset($partner->ecclesia) ? $partner->ecclesia->name . ' (' . $partner->ecclesia->countryName->name . ')' : 'NO NAME' }}
                @endif

                @if ($partner->is_ecclesia_admin == 1)
                    {{-- @dd($partner) --}}
                    <br>

                    <button title="Ecclesia Access" type="button" class="btn btn-primary btn-sm ecclesia-see-button"
                        data-bs-toggle="modal" data-bs-target="#modalEcAccess_{{ $partner->id }}">
                        <i class="ti ti-eye"></i> Ecclesia Access
                    </button>

                    <!-- Modal Body -->
                    <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                    <div class="modal fade" id="modalEcAccess_{{ $partner->id }}" tabindex="-1" data-bs-backdrop="static"
                        data-bs-keyboard="false" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        House Of Ecclesia Access for : <br>{{ $partner->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <ul class="list-group list-group-numbered">
                                        @if (!empty($partner->ecclesia_access))
                                            @foreach ($partner->ecclesia_access as $ecclesia_access)
                                                <li class="list-group-item mb-1">{{ $ecclesia_access->name }}
                                                </li>
                                            @endforeach
                                        @endif

                                    </ul>

                                </div>
                                <div class="modal-footer">
                                    @if (auth()->user()->can('Edit Partners'))
                                        {{-- @if (auth()->user()->hasNewRole('SUPER ADMIN') || $partner->created_id == auth()->user()->id || (auth()->user()->roles()->first()->is_ecclesia == 1 && auth()->id() != $partner->id)) --}}
                                        <a href="{{ route('partners.edit', Crypt::encrypt($partner->id)) }}"
                                            type="button" class="btn btn-primary me-3">
                                            Edit Member
                                        </a>
                                        {{-- @endif --}}
                                    @endif
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                        Close
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </td>
            <td class="text-center">
                @if ($partner->userRegisterAgreement)
                    <span class="badge bg-success view-agreement" data-user_id="{{ $partner->id }}"
                        style="cursor: pointer;"><i class="ti ti-check"></i> Yes</span>
                @else
                    <span class="badge bg-danger"><i class="ti ti-x"></i> No</span>
                @endif
            </td>
            <td>

                @if ($partner->warehouses->count() > 0)
                    <br>
                    <ul>
                        @foreach ($partner->warehouses as $warehouse)
                            <li>
                                {{ $warehouse->name }}
                                @if (!$loop->last)
                                    ,
                                @endif

                            </li>
                        @endforeach
                    </ul>
                @endif
            </td>
            {{-- <td>{{ $partner->user_name }}</td>

    <td>{{ $partner->phone }}</td>
    <td>{{ $partner->address }}</td> --}}
            {{-- @dd(auth()->user()->hasNewRole('SUPER ADMIN')) --}}
            @if (auth()->user()->can('Edit Partners'))
                <td>
                    @if (auth()->user()->hasNewRole('SUPER ADMIN') ||
                            $partner->created_id == auth()->user()->id ||
                            (auth()->user()->roles()->first()->is_ecclesia == 1 && auth()->id() != $partner->id))
                        <div class="button-switch">
                            <input style="cursor: pointer;" type="checkbox" id="switch-orange"
                                class="switch toggle-class" data-id="{{ $partner['id'] }}"
                                {{ $partner['status'] ? 'checked' : '' }} />
                            <label for="switch-orange" class="lbl-off"></label>
                            <label for="switch-orange" class="lbl-on"></label>
                        </div>
                    @else
                        <p>
                            <span>
                                {{ $partner->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    @endif
                </td>
            @endif
            @if (auth()->user()->can('Edit Partners') ||
                    auth()->user()->can('Delete Partners') ||
                    auth()->user()->can('View Partners'))
                <td>
                    <div class="d-flex">
                        @if (Auth::user()->can('Edit Partners'))
                            {{-- @if (auth()->user()->hasNewRole('SUPER ADMIN') || $partner->created_id == auth()->user()->id || (auth()->user()->roles()->first()->is_ecclesia == 1 && auth()->id() != $partner->id)) --}}
                            <a href="{{ route('partners.edit', Crypt::encrypt($partner->id)) }}"
                                class="edit_icon me-2">
                                <i class="ti ti-edit"></i>
                            </a>
                            {{-- @endif --}}
                        @endif

                        @if (Auth::user()->can('View Partners'))
                            <a href="{{ route('partners.show', Crypt::encrypt($partner->id)) }}"
                                class="view_icon me-2">
                                <i class="ti ti-eye"></i>
                            </a>
                        @endif
                        @if (Auth::user()->can('Delete Partners'))
                            {{-- @if (auth()->user()->hasNewRole('SUPER ADMIN') || $partner->created_id == auth()->user()->id || (auth()->user()->roles()->first()->is_ecclesia == 1 && auth()->id() != $partner->id)) --}}
                            <a href="javascript:void(0);"
                                data-route="{{ route('partners.delete', Crypt::encrypt($partner->id)) }}"
                                class="delete_icon" id="delete">
                                <i class="ti ti-trash"></i>
                            </a>
                            {{-- @endif --}}
                        @endif

                    </div>

                </td>
            @endif
        </tr>
    @endforeach
    {{-- pagination --}}
    <tr class="toxic">
        <td colspan="11">
            <div class="d-flex justify-content-center">
                {!! $partners->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr class="toxic">
        <td colspan="11" class="text-center">No Data Found</td>
    </tr>
@endif
