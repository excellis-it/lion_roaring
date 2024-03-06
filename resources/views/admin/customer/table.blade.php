@if (count($customers) > 0)
    @foreach ($customers as $key => $customer)
        <tr>
            <td>{{ $customer->full_name }}</td>
            <td>{{ $customer->user_name }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->address }}</td>
            <td>
                <div class="button-switch">
                    <input type="checkbox" id="switch-orange" class="switch toggle-class" data-id="{{ $customer['id'] }}"
                        {{ $customer['status'] ? 'checked' : '' }} />
                    <label for="switch-orange" class="lbl-off"></label>
                    <label for="switch-orange" class="lbl-on"></label>
                </div>
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    <a title="Edit " href="{{ route('customers.edit', $customer->id) }}">
                        <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span></a>
                    <a title="Delete " data-route="{{ route('customers.delete', $customer->id) }}"
                        href="javascipt:void(0);" id="delete"> <span class="trash-icon"><i
                                class="ph ph-trash"></i></span></a>
                </div>
            </td>

        </tr>
    @endforeach
    {{-- pagination --}}
    <tr class="toxic">
        <td colspan="7" >
            <div class="d-flex justify-content-center">
                {!! $customers->links() !!}
            </div>
        </td>
    </tr>
    @else
    <tr class="toxic">
        <td colspan="7" class="text-center">No Data Found</td>
    </tr>
@endif
