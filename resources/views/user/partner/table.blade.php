@if (count($partners) > 0)
    @foreach ($partners as $key => $partner)
        <tr>
            <td>
                <div class="toggle-check">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="" value="" id="">
                    </div>
                </div>
            </td>
            <td>{{ $partner->email }}</td>
            <td>{{ $partner->full_name }}</td>
            <td>{{$partner->getRoleNames()->first()}}</td>            
            @if (auth()->user()->can('Edit Partners') || auth()->user()->can('Delete Partners') || auth()->user()->can('View Partners'))
            <td>
                <div class="d-flex">
                    @if (Auth::user()->can('Edit Partners'))
                    <a href="{{route('partners.edit', Crypt::encrypt($partner->id))}}" class="edit_icon me-2">
                        <i class="ti ti-edit"></i>
                    </a>
                    @endif
                </div>
            </td>
            @endif
        </tr>
    @endforeach
    {{-- pagination --}}
    <tr class="toxic">
        <td colspan="10" >
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
