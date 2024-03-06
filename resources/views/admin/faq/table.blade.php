@if (count($faqs) > 0)
@foreach ($faqs as $key => $faq)
    <tr>
        <td> {{ ($faqs->currentPage()-1) * $faqs->perPage() + $loop->index + 1 }}</td>
        <td>{{ $faq->question }}</td>
        <td>{{ $faq->answer }}</td>
        <td>
            <div class="edit-1 d-flex align-items-center justify-content-center">
                <a title="Edit" href="{{ route('faq.edit', $faq->id) }}">
                    <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                </a>
                <a title="Delete" data-route="{{ route('faq.delete', $faq->id) }}"
                    href="javascript:void(0);" id="delete">
                    <span class="trash-icon"><i class="ph ph-trash"></i></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
<tr style="box-shadow: none;">
    <td colspan="5">
        <div class="d-flex justify-content-center">
            {!! $faqs->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="5" class="text-center">No FAQ Found</td>
</tr>
@endif
