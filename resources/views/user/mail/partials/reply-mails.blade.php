@if ($reply_mails->count() > 0)
    @foreach ($reply_mails as $reply)
        <div class="reply-mail card card-body">
            <div class="mail_subject">
                <h5>{{ $reply->id }}</h5>
                {{-- <h6>From: {{ $reply->user->email }}</h6>
                <p>{{ $reply->message }}</p> --}}
            </div>


        </div>
    @endforeach
@endif
