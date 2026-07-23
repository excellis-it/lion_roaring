@php
    $ragWidgetUrl = env('RAG_WIDGET_URL');
    $ragApiBase = env('RAG_API_BASE');
    $ragBotId = env('RAG_BOT_ID');
    $ragAuthToken = env('RAG_AUTH_TOKEN');
    $ragWidgetReady = $ragWidgetUrl && $ragApiBase && $ragBotId && $ragAuthToken;
@endphp
@if ($ragWidgetReady)
    @php
        $ragWidgetConfig = [
            'apiBase' => $ragApiBase,
            'botId' => $ragBotId,
            'authToken' => $ragAuthToken,
            'hostLanguagesUrl' => route('chatbot.languages'),
            'hostLanguageChangeUrl' => route('chatbot.language'),
            'hostSearchUrl' => route('chatbot.search-keywords'),
            'hostProductUrl' => route('e-store.product-details', ['slug' => ':slug']),
            'hostFaqUrl' => route('chatbot.faq-questions'),
            'hostFeedbackUrl' => route('chatbot.feedback'),
            'hostEstoreSearchUrl' => route('chatbot.search-estore'),
            'hostElearningSearchUrl' => route('chatbot.search-elearning'),
            'hostInitUrl' => route('chatbot.init'),
            'hostGuestNameUrl' => route('chatbot.guest-name'),
            'hostMessageUrl' => route('chatbot.message'),
            'csrfToken' => csrf_token(),
            'isAuthenticated' => Auth::check(),
            'userName' => Auth::check()
                ? trim(Auth::user()->first_name . ' ' . Auth::user()->last_name)
                : '',
        ];
    @endphp
    <!-- Normal chatbot fallback (hidden until the AI widget fails to load) -->
    <div id="chatbot-fallback" style="display: none;">
        @include('frontend.includes.chatbot')
    </div>

    <!-- RAG Chatbot Widget -->
    <script id="rag-widget-config" type="application/json">@json($ragWidgetConfig)</script>
    <script>
        (function () {
            var FALLBACK_TIMEOUT = 8000;
            var settled = false;

            function showFallback() {
                if (settled) return;
                settled = true;
                var el = document.getElementById('chatbot-fallback');
                if (el) el.style.display = '';
            }

            function initWidget() {
                if (settled) return;
                if (!window.RAGWidget) {
                    showFallback();
                    return;
                }
                settled = true;
                var configEl = document.getElementById('rag-widget-config');
                var config = JSON.parse(configEl.textContent);
                window.RAGWidget.init(config);
            }

            var s = document.createElement('script');
            s.src = @json($ragWidgetUrl);
            s.async = true;
            s.onload = initWidget;
            s.onerror = showFallback;
            document.head.appendChild(s);

            // If the widget never loads or never responds, fall back to the normal chatbot.
            setTimeout(function () {
                if (settled) return;
                window.RAGWidget ? initWidget() : showFallback();
            }, FALLBACK_TIMEOUT);
        })();
    </script>
@else
    @include('frontend.includes.chatbot')
@endif
