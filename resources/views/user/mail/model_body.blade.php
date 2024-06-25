@if (isset($viewMail))
    <p><strong>Subject:</strong> {{$viewMail->subject}}</p>
    <p><strong>To:</strong> {{$viewMail->to}}</p>
    <p><strong>CC:</strong> {{$viewMail->cc ?? ''}}</p>
    <p><strong>Message:</strong></p>
    <p>{!! nl2br($viewMail->message) !!}</p>
@endif
