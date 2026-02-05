@component('mail::message')
    <h1>Welcome, {{ $maildata['name'] }}! 🎉</h1>

<p>
    We’re excited to have you on board! Thank you for creating an account with us.
</p>

@if ($maildata['status'] == 1)
    <p>
        <strong>Good news!</strong> Your account has been successfully activated.
    </p>

    <p>
        You can now log in and start exploring all the features and services we offer.
        We’re confident you’ll have a great experience with our platform.
    </p>

    <p>
        If you need any assistance, have questions, or require support at any point,
        our team is always here to help.
    </p>

    <p>
        👉 <strong>Next step:</strong> Log in to your account and get started today.
    </p>
@else
    <p>
        Thank you for registering with us. Your account is currently
        <strong>pending admin approval</strong>.
    </p>

    <p>
        Once your account has been reviewed and approved, you will receive a confirmation email,
        and you’ll be able to log in and access our services.
    </p>

    <p>
        We appreciate your patience and understanding during this process.
    </p>
@endif

<p>
    If you have any questions or need further assistance, please don’t hesitate to contact us.
</p>

<p>
    Thanks &amp; regards,<br>
    <strong>{{ config('app.name') }}</strong><br>
    Support Team
</p>
@endcomponent
