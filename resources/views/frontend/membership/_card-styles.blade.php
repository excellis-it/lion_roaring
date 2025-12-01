@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.tier-card').each(function(i) {
                $(this).css({
                    opacity: 0,
                    transform: 'translateY(8px)'
                });
                setTimeout(() => {
                    $(this).css({
                        transition: 'opacity .5s ease, transform .5s ease',
                        opacity: 1,
                        transform: 'translateY(0)'
                    });
                }, 60 * i);
            });
        });
    </script>
@endpush
