@if (count($faqs) > 0)
    @foreach ($faqs as $key => $faq)
        <tr>
            <td> {{ ($faqs->currentPage() - 1) * $faqs->perPage() + $loop->index + 1 }}</td>
            <td>{{ $faq->question }}</td>
            <td>
                <td>
                    @php
                        $message = $faq->answer ?? 'N/A';
                        $shortMessage = strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                    @endphp
                    <span class="short-message">{{ $shortMessage }}</span>
                    <span class="full-message" style="display:none">{{ $message }}</span>
                    @if(strlen($message) > 50)
                        <a href="javascript:void(0)" class="toggle-message">Show More</a>
                    @endif
                </td></td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    @if (auth()->user()->can('Edit Faq'))
                        <a title="Edit" href="{{ route('faq.edit', $faq->id) }}">
                            <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                        </a>
                    @endif
                    @if (auth()->user()->can('Delete Faq'))
                        <a title="Delete" data-route="{{ route('faq.delete', $faq->id) }}" href="javascript:void(0);"
                            id="delete">
                            <span class="trash-icon"><i class="fas fa-trash"></i></span>
                        </a>
                    @endif
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


@push('scripts')
    <script>
        $(document).ready(function() {
            $('.toggle-message').click(function() {
                $(this).closest('td').find('.short-message').toggle();
                $(this).closest('td').find('.full-message').toggle();
                $(this).text($(this).text() === 'Show More' ? 'Show Less' : 'Show More');
            });
        });
    </script>
@endpush
