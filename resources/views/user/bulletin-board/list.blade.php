@extends('user.layouts.master')
@section('title')
    Bulletin Board - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        #show-bulletin {
            position: relative;
            min-height: 160px;
        }

        #show-bulletin.is-translating > .bulletin-item,
        #show-bulletin.is-translating > .text-center {
            display: none !important;
        }

        #bulletin-skel-overlay {
            display: none;
            width: 100%;
        }

        #show-bulletin.is-translating #bulletin-skel-overlay {
            display: block;
        }

        .bulletin-skel-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            max-width: 920px;
            margin: 0 0 28px;
        }

        .bulletin-skel-row.is-right {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .bulletin-skel-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            flex: 0 0 64px;
            background: linear-gradient(110deg, #e8e0ef 8%, #f6f1fa 18%, #e8e0ef 33%);
            background-size: 200% 100%;
            animation: bulletinSkelShine 1.15s linear infinite;
            position: relative;
        }

        .bulletin-skel-avatar::after {
            content: '';
            position: absolute;
            right: 2px;
            bottom: -14px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbb6d8;
        }

        .bulletin-skel-row.is-right .bulletin-skel-avatar::after {
            right: auto;
            left: 2px;
        }

        .bulletin-skel-bubble {
            position: relative;
            flex: 1 1 auto;
            min-width: 0;
            max-width: min(640px, calc(100% - 76px));
            padding: 18px 16px 14px;
            border: 1px solid rgba(0, 0, 0, 0.55);
            border-radius: 10px;
            box-shadow: inset 0 0 9px rgb(100 50 113 / 28%);
            background: #fff;
        }

        .bulletin-skel-name {
            position: absolute;
            top: -11px;
            right: 16px;
            width: 110px;
            height: 18px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 0 0 4px #fff;
            overflow: hidden;
        }

        .bulletin-skel-row.is-right .bulletin-skel-name {
            right: auto;
            left: 16px;
        }

        .bulletin-skel-name::before,
        .bulletin-skel-line {
            display: block;
            border-radius: 999px;
            background: linear-gradient(110deg, #e8e0ef 8%, #f6f1fa 18%, #e8e0ef 33%);
            background-size: 200% 100%;
            animation: bulletinSkelShine 1.15s linear infinite;
        }

        .bulletin-skel-name::before {
            content: '';
            width: 100%;
            height: 100%;
        }

        .bulletin-skel-line {
            height: 12px;
            margin-bottom: 12px;
        }

        .bulletin-skel-line.title {
            width: 48%;
            height: 16px;
            margin-top: 4px;
            margin-bottom: 14px;
        }

        .bulletin-skel-line.w92 { width: 92%; }
        .bulletin-skel-line.w78 { width: 78%; }
        .bulletin-skel-line.w56 { width: 56%; margin-bottom: 0; }

        .bulletin-skel-time {
            width: 120px;
            height: 10px;
            margin-top: 10px;
            margin-left: auto;
            border-radius: 999px;
            background: linear-gradient(110deg, #e8e0ef 8%, #f6f1fa 18%, #e8e0ef 33%);
            background-size: 200% 100%;
            animation: bulletinSkelShine 1.15s linear infinite;
        }

        .bulletin-skel-row.is-right .bulletin-skel-time {
            margin-left: 0;
            margin-right: auto;
        }

        @keyframes bulletinSkelShine {
            to { background-position-x: -200%; }
        }

        @media (max-width: 991.98px) {
            .bulletin-skel-row {
                max-width: 100%;
                margin-bottom: 22px;
                gap: 10px;
            }

            .bulletin-skel-avatar {
                width: 50px;
                height: 50px;
                flex-basis: 50px;
            }

            .bulletin-skel-bubble {
                max-width: calc(100% - 60px);
                padding: 16px 12px 12px;
            }

            .bulletin-skel-name {
                width: 88px;
                height: 16px;
                right: 12px;
            }

            .bulletin-skel-row.is-right .bulletin-skel-name {
                left: 12px;
            }

            .bulletin-skel-line.title {
                width: 62%;
                height: 14px;
            }

            .bulletin-skel-time {
                width: 96px;
            }
        }

        @media (max-width: 575.98px) {
            .bulletin-skel-row {
                gap: 8px;
                margin-bottom: 18px;
            }

            .bulletin-skel-avatar {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }

            .bulletin-skel-avatar::after {
                display: none;
            }

            .bulletin-skel-bubble {
                max-width: calc(100% - 48px);
                padding: 14px 10px 10px;
            }

            .bulletin-skel-name {
                width: 72px;
                right: 10px;
            }

            .bulletin-skel-line {
                height: 10px;
                margin-bottom: 10px;
            }

            .bulletin-skel-line.title {
                width: 70%;
                height: 12px;
                margin-bottom: 12px;
            }

            .bulletin-skel-time {
                width: 84px;
                height: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .bulletin-skel-avatar,
            .bulletin-skel-name::before,
            .bulletin-skel-line,
            .bulletin-skel-time {
                animation: none;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp">
                        <h2>Bulletin Board</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Bulletin Chat</h2>
                </div>
                <div class="bulletin_board" id="show-bulletin">
                    @include('user.bulletin-board.show-bulletin')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function getBulletinTargetLang() {
                var gt = document.cookie.match(/(?:^|;\s*)googtrans=\/auto\/([^;]+)/);
                if (gt && gt[1]) {
                    return decodeURIComponent(gt[1]).split('-')[0].toLowerCase();
                }
                var cl = document.cookie.match(/(?:^|;\s*)content_lang=([^;]+)/);
                if (cl && cl[1]) {
                    var code = decodeURIComponent(cl[1]).toLowerCase();
                    if (code && code !== '__original__') {
                        return code.split('-')[0];
                    }
                }
                return null;
            }

            function ensureSkelOverlay(board) {
                var overlay = document.getElementById('bulletin-skel-overlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'bulletin-skel-overlay';
                    overlay.setAttribute('aria-hidden', 'true');
                    board.appendChild(overlay);
                }
                return overlay;
            }

            function buildSkeletonCards(count) {
                var n = Math.min(Math.max(count || 3, 2), 6);
                var html = '';
                for (var i = 0; i < n; i++) {
                    var isRight = i % 2 === 1;
                    html +=
                        '<div class="bulletin-skel-row' + (isRight ? ' is-right' : '') + '" aria-hidden="true">' +
                            '<div class="bulletin-skel-avatar"></div>' +
                            '<div class="bulletin-skel-bubble">' +
                                '<span class="bulletin-skel-name"></span>' +
                                '<span class="bulletin-skel-line title"></span>' +
                                '<span class="bulletin-skel-line w92"></span>' +
                                '<span class="bulletin-skel-line w78"></span>' +
                                '<span class="bulletin-skel-line w56"></span>' +
                                '<div class="bulletin-skel-time"></div>' +
                            '</div>' +
                        '</div>';
                }
                return html;
            }

            function showBulletinSkeleton(board) {
                if (!board) {
                    return;
                }
                var count = board.querySelectorAll('.bulletin-item').length;
                var overlay = ensureSkelOverlay(board);
                overlay.innerHTML = buildSkeletonCards(count);
                board.classList.add('is-translating');
            }

            function hideBulletinSkeleton(board) {
                if (!board) {
                    return;
                }
                board.classList.remove('is-translating');
                var overlay = document.getElementById('bulletin-skel-overlay');
                if (overlay) {
                    overlay.innerHTML = '';
                }
            }

            window.applyBulletinBoardTranslations = function () {
                var target = getBulletinTargetLang();
                var board = document.getElementById('show-bulletin');
                if (!target || !board || !board.querySelector('.bulletin-item')) {
                    hideBulletinSkeleton(board);
                    return;
                }

                showBulletinSkeleton(board);

                fetch(@json(route('bulletin-board.translate-content')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ target: target })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        (data.items || []).forEach(function (item) {
                            var el = board.querySelector('[data-bulletin-id="' + item.id + '"]');
                            if (!el) {
                                return;
                            }
                            var title = el.querySelector('.bulletin-title');
                            var desc = el.querySelector('.bulletin-description');
                            if (title && typeof item.title === 'string') {
                                title.textContent = item.title;
                            }
                            if (desc && typeof item.description_html === 'string') {
                                desc.innerHTML = item.description_html;
                            }
                        });
                    })
                    .catch(function () {
                        // Keep originals on failure
                    })
                    .finally(function () {
                        hideBulletinSkeleton(board);
                    });
            };

            window.reloadBulletinBoardOriginal = function () {
                var board = document.getElementById('show-bulletin');
                if (!board) {
                    window.location.reload();
                    return;
                }
                showBulletinSkeleton(board);
                fetch(@json(route('bulletin-board.load')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.view) {
                            board.innerHTML = data.view;
                        }
                    })
                    .catch(function () {
                        window.location.reload();
                    })
                    .finally(function () {
                        hideBulletinSkeleton(board);
                    });
            };

            document.addEventListener('DOMContentLoaded', function () {
                if (getBulletinTargetLang()) {
                    showBulletinSkeleton(document.getElementById('show-bulletin'));
                }
                window.applyBulletinBoardTranslations();
            });
        })();
    </script>
@endpush
