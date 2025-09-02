@if (count($partners) > 0)
    @foreach ($partners as $key => $partner)
        <tr>
            <td class="p-3">
                {{ $partners->firstItem() + $key }}
            </td>
            <td class="p-3">{{ $partner->email }}</td>
            <td class="p-3">{{ $partner->full_name }}</td>

            <td class="p-3">{{ $partner->phone }}</td>

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
