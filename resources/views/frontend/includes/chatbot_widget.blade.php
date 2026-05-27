@php
    $chatbotMode = env('CHATBOT');
@endphp
@if ($chatbotMode === 'AI')
    @include('frontend.includes.ai_chatbot')
@elseif ($chatbotMode === 'RAG')
    @include('frontend.includes.rag_chatbot')
@else
    @include('frontend.includes.chatbot')
@endif
