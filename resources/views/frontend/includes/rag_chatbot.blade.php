<!-- RAG Chatbot Widget -->
<script src="http://localhost:10046/ragChatWidget.js"></script>
<script>
  window.RAGWidget.init({
    apiBase: "http://localhost:10047/api",
    botId: "69fb16097653e4bdd23386ea",
    authToken: "d098d32e615f11fb9bfa5daa450375834df6cdefd006f7f250727415e4420565",
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
