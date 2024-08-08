@if (count($ecclesias) > 0)
@foreach ($ecclesias as $key => $ecclesia)
    <tr>
        <td>
            {{ $ecclesias->firstItem() + $key }}
        </td>
        <td>{{ $ecclesia->name }}</td>
        <td>
            {{ $ecclesia->country ? $ecclesia->countryName->name : '-' }}
        </td>
        <td>
            <div class="d-flex">
                <a href="{{ route('ecclesias.edit', Crypt::encrypt($ecclesia->id)) }}"
                    class="edit_icon me-2">
                    <i class="ti ti-edit"></i>
                </a>
                <a href="javascript:void(0);"
                    data-route="{{ route('ecclesias.delete', Crypt::encrypt($ecclesia->id)) }}"
                    class="delete_icon" id="delete">
                    <i class="ti ti-trash"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
{{-- pagination --}}
<tr class="toxic">
    <td colspan="4">
        <div class="d-flex justify-content-center">
            {!! $ecclesias->links() !!}
        </div>
    </td>
</tr>
@else
<tr class="toxic">
    <td colspan="4" class="text-center">No Data Found</td>
</tr>
@endif
