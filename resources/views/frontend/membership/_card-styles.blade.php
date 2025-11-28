@push('styles')
    <style>
        :root {
            --theme: #643271;
            --theme-dark: #4b254f;
            --theme-50: rgba(100, 50, 113, 0.06);
            --theme-25: rgba(100, 50, 113, 0.02);
        }

        .tier-card {
            position: relative;
            border-radius: .8rem;
            transition: transform .25s ease, box-shadow .25s ease;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .tier-card::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            right: 0;
            height: 6px;
            border-top-left-radius: .8rem;
            border-top-right-radius: .8rem;
            background: linear-gradient(90deg, var(--theme), var(--theme-dark));
        }

        .tier-card::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 4px;
            border-bottom-left-radius: .8rem;
            border-bottom-right-radius: .8rem;
            background: linear-gradient(90deg, var(--theme-25), rgba(100, 50, 113, 0.02));
        }

        .tier-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 44px rgba(100, 50, 113, 0.14);
        }

        .ribbon {
            position: absolute;
            top: 12px;
            right: -30px;
            background: var(--theme);
            color: #fff;
            padding: 6px 40px;
            transform: rotate(45deg);
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(100, 50, 113, 0.12);
        }

        .badge-price {
            background: linear-gradient(90deg, var(--theme-50), var(--theme-25));
            border: 1px solid rgba(100, 50, 113, 0.12);
            padding: .5rem .8rem;
            border-radius: .5rem;
        }

        .btn-upgrade {
            background: var(--theme);
            border-color: var(--theme);
            color: #fff;
            padding: .6rem 1rem;
            font-weight: 700;
        }

        .btn-upgrade:hover {
            background: var(--theme-dark);
            border-color: var(--theme-dark);
            box-shadow: 0 10px 30px rgba(100, 50, 113, 0.12);
        }
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
