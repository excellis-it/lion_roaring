@php
    $donationPopupConfig = [
        'sessions' => session()->all(),
        'isDonation' => request('is_donation') === 'yes',
        'hasAgree' => session()->has('agree'),
    ];
@endphp
<script id="donation-popup-config" type="application/json">@json($donationPopupConfig)</script>
<script>
    $(document).ready(function () {
        var cfg = JSON.parse(document.getElementById('donation-popup-config').textContent);

        setTimeout(function () {
            if (cfg.isDonation) {
                $('#onload_popup').modal('hide');
                $('#exampleModalToggle2').modal('show');
            } else if (!cfg.hasAgree) {
                $('#onload_popup').modal('show');
            }
        }, 300);
    });
</script>
