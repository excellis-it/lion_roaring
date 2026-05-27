@php
    $aiWidgetUrl = env('RAG_WIDGET_URL');
    $aiApiBase = env('RAG_API_BASE');
    $aiBotId = env('RAG_BOT_ID');
    $aiAuthToken = env('RAG_AUTH_TOKEN');
    $aiWidgetReady = $aiWidgetUrl && $aiApiBase && $aiBotId && $aiAuthToken;
@endphp
@if ($aiWidgetReady)
    @php
        $aiWidgetConfig = [
            'apiBase' => $aiApiBase,
            'botId' => $aiBotId,
            'authToken' => $aiAuthToken,
        ];
    @endphp
    <script src="{{ $aiWidgetUrl }}"></script>
    <script id="ai-widget-config" type="application/json">@json($aiWidgetConfig)</script>
    <script>
        (function () {
            if (!window.RAGWidget) {
                return;
            }
            var config = JSON.parse(document.getElementById('ai-widget-config').textContent);
            window.RAGWidget.init(config);
        })();
    </script>
@endif
