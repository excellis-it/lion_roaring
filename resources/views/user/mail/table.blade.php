@if (count($mails) > 0)
    @foreach ($mails as $key => $mail)
        <tr>
            <td>{{ $mails->firstItem() + $key }}</td>
            <td> {{ $mail->to }}</td>
            <td> {{ $mail->cc }}</td>
            <td> {{ $mail->subject }}</td>
            <td>
                <div class="d-flex">
                    <a href="javascript:void(0);" class="edit_icon me-2 view_details" data-id="{{ $mail->id }}">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="javascript:void(0)" id="delete" data-route="{{ route('mail.delete', $mail->id) }}"
                        class="delete_icon">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>

            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="5">
            <div class="d-flex justify-content-center">
                {!! $mails->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No data found</td>
    </tr>
@endif
