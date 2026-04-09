<!-- RAG Chatbot Widget -->
<script src="http://localhost:5000/ragChatWidget.js"></script>
<script>
  window.RAGWidget.init({
    apiBase: "http://localhost:5000/api",
    botId: "69d605fab71466ec512c6198",
    authToken: "2f5865412065d3d1b16e39dd52b1d422e781f5e3014c2e48748ff5121e69a16e",
    // Host page integration
    hostLanguagesUrl: "{{ route('chatbot.languages') }}",
    hostSearchUrl: "{{ route('chatbot.search-keywords') }}",
    hostProductUrl: "{{ route('e-store.product-details', ['slug' => ':slug']) }}",
    hostFaqUrl: "{{ route('chatbot.faq-questions') }}",
    hostFeedbackUrl: "{{ route('chatbot.feedback') }}",
    hostEstoreSearchUrl: "{{ route('chatbot.search-estore') }}",
    hostElearningSearchUrl: "{{ route('chatbot.search-elearning') }}",
    hostInitUrl: "{{ route('chatbot.init') }}",
    hostGuestNameUrl: "{{ route('chatbot.guest-name') }}",
    hostMessageUrl: "{{ route('chatbot.message') }}",
    csrfToken: "{{ csrf_token() }}",
    isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
    userName: "{{ Auth::check() ? trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) : '' }}"
  });
</script>
