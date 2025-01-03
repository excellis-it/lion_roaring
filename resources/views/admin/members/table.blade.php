@if (count($partners) > 0)
    @foreach ($partners as $key => $partner)
        <tr>
            <td>
                {{ $partners->firstItem() + $key }}
            </td>
            <td>{{ $partner->full_name }}</td>
            <td>
                {{ $partner->ecclesia ?  $partner->ecclesia->full_name : '' }}
            </td>
            <td>{{ $partner->user_name }}</td>
            <td>{{ $partner->email }}</td>
            <td>{{ $partner->phone }}</td>
            <td>{{ $partner->address }}</td>
            <td>{{$partner->getRoleNames()->first()}}</td>
            <td>
                @if ($partner->is_accept == 1)
                    <span>Accepted</span>
                @else
                    <form action="{{ route('members.accept', $partner->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="ph ph-check"></i> Accept</button>
                    </form>
                @endif
            </td>
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
