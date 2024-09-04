@component('mail::message')
<h1>We have received your request to reset your account username</h1>
<p>You can click the following button to change your username</p>

@component('mail::button', ['url' => route('user.reset.username', ['id'=>$details['id'],'token'=>$details['token']])])
    Change Username
@endcomponent


<p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent
