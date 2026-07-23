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
    <!-- RAG Chatbot Widget -->
    <script src="{{ $ragWidgetUrl }}"></script>
    <script id="rag-widget-config" type="application/json">@json($ragWidgetConfig)</script>
    <script>
        (function () {
            if (!window.RAGWidget) {
                return;
            }
            var configEl = document.getElementById('rag-widget-config');
            var config = JSON.parse(configEl.textContent);
            window.RAGWidget.init(config);
        })();
    </script>
@endif
