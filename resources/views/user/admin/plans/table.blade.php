@if (count($plans) > 0)
    @foreach ($plans as $key => $plan)
        <tr>
            <td>{{ $plan->plan_name }}</td>
            <td>{{ $plan->plan_price }}</td>
            <td>{{ $plan->plan_validity }}</td>
            <td>
                <div class="button-switch">
                    <input type="checkbox" id="switch-orange" class="switch toggle-class" data-id="{{ $plan['id'] }}"
                        {{ $plan['plan_status'] ? 'checked' : '' }} />
                    <label for="switch-orange" class="lbl-off"></label>
                    <label for="switch-orange" class="lbl-on"></label>
                </div>
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    <a title="Edit " href="{{ route('plans.edit', $plan->id) }}">
                        <span class="edit-icon"><i class="fas fa-edit"></i></span></a>
                    <a title="Delete " data-route="{{ route('plans.delete', $plan->id) }}"
                        href="javascipt:void(0);" id="delete"> <span class="trash-icon"><i
                                class="fas fa-trash"></i></span></a>
                </div>
            </td>

        </tr>
    @endforeach
    {{-- pagination --}}
    <tr class="toxic">
        <td colspan="7" >
            <div class="d-flex justify-content-center">
                {!! $plans->links() !!}
            </div>
        </td>
    </tr>
    @else
    <tr class="toxic">
        <td colspan="7" class="text-center">No Data Found</td>
    </tr>
@endif
