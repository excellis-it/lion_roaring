@php
    $cardTypeImages = [
        'visa' => asset('frontend_assets/images/visa.png'),
        'mastercard' => asset('frontend_assets/images/mastercard.png'),
        'amex' => asset('frontend_assets/images/amex.png'),
        'unknown' => asset('frontend_assets/images/unknown.webp'),
    ];
@endphp
<script id="card-type-images-data" type="application/json">@json($cardTypeImages)</script>
<script>
    $(document).ready(function () {
        var cardTypeImages = JSON.parse(document.getElementById('card-type-images-data').textContent);

        $('#card-number').on('keyup change', function () {
            var cardNumber = $(this).val();
            var cardType = $.payment.cardType(cardNumber);
            var cardTypeImage = cardTypeImages[cardType] || cardTypeImages.unknown;
            $('#card-type-image').attr('src', cardTypeImage);

            var cvvLength = cardType === 'amex' ? 4 : 3;
            $('#card-cvc').attr('maxlength', cvvLength);
        });
    });
</script>
