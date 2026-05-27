@php
    $socketInit = [
        'ipAddress' => env('IP_ADDRESS'),
        'socketPort' => env('SOCKET_PORT'),
        'senderId' => auth()->user()->id,
        'role' => auth()->user()->hasNewRole('SUPER ADMIN') ? 'admin' : 'user',
    ];
@endphp
<script id="socket-init-data" type="application/json">@json($socketInit)</script>
