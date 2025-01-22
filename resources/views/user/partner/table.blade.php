@if (count($partners) > 0)
    @foreach ($partners as $key => $partner)
        <tr>
            <td>
                {{ $partners->firstItem() + $key }}
            </td>
            <td>{{ $partner->full_name }}</td>
            <td>
                {{ isset($partner->ecclesia) ? $partner->ecclesia->full_name : '' }}
            </td>
            <td>{{ $partner->user_name }}</td>
            <td>{{ $partner->email }}</td>
            <td>{{ $partner->phone }}</td>
            <td>{{ $partner->address }}</td>
            <td>{{ $partner->getRoleNames()->first() }}</td>
            @if (auth()->user()->hasRole('SUPER ADMIN') ||
                    (auth()->user()->hasRole('LEADER') && auth()->user()->can('Edit Partners')))
                <td>
                    <div class="button-switch">
                        <input type="checkbox" id="switch-orange" class="switch toggle-class" data-id="{{ $partner['id'] }}"
                            {{ $partner['status'] ? 'checked' : '' }} />
                        <label for="switch-orange" class="lbl-off"></label>
                        <label for="switch-orange" class="lbl-on"></label>
                    </div>
                </td>
            @endif
            @if (auth()->user()->can('Edit Partners') ||
                    auth()->user()->can('Delete Partners') ||
                    auth()->user()->can('View Partners'))
                <td>
                    <div class="d-flex">
                        @if (Auth::user()->can('Edit Partners'))
                            <a href="{{ route('partners.edit', Crypt::encrypt($partner->id)) }}"
                                class="edit_icon me-2">
                                <i class="ti ti-edit"></i>
                            </a>
                        @endif

                        @if (Auth::user()->can('View Partners'))
                            <a href="{{ route('partners.show', Crypt::encrypt($partner->id)) }}"
                                class="view_icon me-2">
                                <i class="ti ti-eye"></i>
                            </a>
                        @endif
                        @if (Auth::user()->can('Delete Partners'))
                            <a href="javascript:void(0);"
                                data-route="{{ route('partners.delete', Crypt::encrypt($partner->id)) }}"
                                class="delete_icon" id="delete">
                                <i class="ti ti-trash"></i>
                            </a>
                        @endif

                    </div>

                </td>
            @endif
        </tr>
    @endforeach
    {{-- pagination --}}
    <tr class="toxic">
        <td colspan="10">
            <div class="d-flex justify-content-center">
                {!! $partners->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr class="toxic">
        <td colspan="10" class="text-center">No Data Found</td>
    </tr>
@endif
