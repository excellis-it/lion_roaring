@php
    $toastrMessages = [];
    foreach (
        ['message' => 'success', 'error' => 'error', 'info' => 'info', 'warning' => 'warning'] as $sessionKey => $toastrType
    ) {
        if (session()->has($sessionKey)) {
            $toastrMessages[] = ['type' => $toastrType, 'text' => session($sessionKey)];
        }
    }
    $toastrPositionClass = $toastrPositionClass ?? 'toast-bottom-right';
@endphp
@if (count($toastrMessages))
    <script id="toastr-flash-data" type="application/json">@json(['messages' => $toastrMessages, 'positionClass' => $toastrPositionClass])</script>
    <script>
        (function () {
            var data = JSON.parse(document.getElementById('toastr-flash-data').textContent);
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: data.positionClass,
                timeOut: '3000',
            };
            data.messages.forEach(function (item) {
                toastr[item.type](item.text);
            });
        })();
    </script>
@endif
