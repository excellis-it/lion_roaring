@component('mail::message')
<h1>We have received your request to reset your account password</h1>
<p>You can click the following button to recover your account:</p>

@component('mail::button', ['url' => route('admin.reset.password', ['id'=>$details['id'],'token'=>$details['token']])])
    Reset Password
@endcomponent


<p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent

