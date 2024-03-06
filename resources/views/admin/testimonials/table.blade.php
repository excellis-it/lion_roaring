@if (count($testimonials) > 0)
@foreach ($testimonials as $key => $testimonial)
    <tr>
        <td> {{ ($testimonials->currentPage()-1) * $testimonials->perPage() + $loop->index + 1 }}</td>
        <td>{{ $testimonial->name }}</td>
        <td>{{ $testimonial->address }}</td>
        <td>{{ Str::words($testimonial->description, $words = 20, $end = '...') }}
        </td>
        <td>
            <div class="edit-1 d-flex align-items-center justify-content-center">
                <a title="Edit" href="{{ route('testimonials.edit', $testimonial->id) }}">
                    <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                </a>
                <a title="Delete" data-route="{{ route('testimonials.delete', $testimonial->id) }}"
                    href="javascript:void(0);" id="delete">
                    <span class="trash-icon"><i class="ph ph-trash"></i></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
<tr style="box-shadow: none;">
    <td colspan="8">
        <div class="d-flex justify-content-center">
            {!! $testimonials->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="8" class="text-center">No Testimonial Found</td>
</tr>
@endif
