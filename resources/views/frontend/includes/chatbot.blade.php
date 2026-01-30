<!-- Chatbot Widget -->
<style>
    :root {
        --chatbot-primary: #643271;
        --chatbot-secondary: #d98b1c;
        --chatbot-bg-light: #f9fafb;
        --chatbot-text-dark: #1a1a1a;
        --chatbot-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* Floating Button */
    .chatbot-float-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--chatbot-primary), #7b3a8a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: var(--chatbot-shadow);
        z-index: 9999;
        transition: all 0.3s ease;
        border: none;
        outline: none;
    }

    .chatbot-float-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(100, 50, 113, 0.3);
    }

    .chatbot-float-btn .close-icon {
        display: none;
    }

    .chatbot-float-btn.active .chat-icon {
        display: none;
    }

    .chatbot-float-btn.active .close-icon {
        display: block;
    }

    /* Chat Window */
    .chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 400px;
        height: 600px;
        background: white;
        border-radius: 20px;
        box-shadow: var(--chatbot-shadow);
        z-index: 9998;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .chatbot-window.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    /* Internal Header */
    .chatbot-widget-header {
        background: linear-gradient(135deg, var(--chatbot-primary), #7b3a8a);
        padding: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chatbot-widget-avatar {
        width: 40px;
        height: 40px;
        background: var(--chatbot-secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
.chatbot-widget-avatar img{
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: contain;
}
    .chatbot-widget-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: white;
    }

    .chatbot-widget-status {
        font-size: 12px;
        opacity: 0.8;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .chatbot-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: chatbot-pulse 2s infinite;
    }

    @keyframes chatbot-pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    /* Messages Area */
    .chatbot-widget-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: var(--chatbot-bg-light);
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .chatbot-widget-messages::-webkit-scrollbar {
        width: 5px;
    }

    .chatbot-widget-messages::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .chat-msg {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 15px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        animation: chatSlideIn 0.3s ease-out;
    }

    @keyframes chatSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-msg.bot {
        align-self: flex-start;
        background: white;
        color: var(--chatbot-text-dark);
        border-bottom-left-radius: 2px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .chat-msg.bot p {
        margin: 0 0 10px 0 !important;
        font-family: inherit !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
    }

    .chat-msg.bot p:last-child {
        margin-bottom: 0 !important;
    }

    .chat-msg.bot * {
        max-width: 100%;
        word-break: break-word;
    }

    .chat-msg.user {
        align-self: flex-end;
        background: var(--chatbot-primary);
        color: white;
        border-bottom-right-radius: 2px;
    }

    /* Footer Input */
    .chatbot-widget-footer {
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }

    .chatbot-widget-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 25px;
        padding: 10px 15px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }

    .chatbot-widget-input:focus {
        border-color: var(--chatbot-primary);
    }

    .chatbot-widget-send {
        width: 40px;
        height: 40px;
        background: var(--chatbot-primary);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }

    .chatbot-widget-send:hover {
        transform: scale(1.1);
    }

    /* Quick Reply Buttons */
    .widget-quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 5px;
    }

    .widget-reply-btn {
        padding: 8px 14px;
        background: white;
        border: 1px solid var(--chatbot-primary);
        color: var(--chatbot-primary);
        border-radius: 18px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .widget-reply-btn:hover {
        background: var(--chatbot-primary);
        color: white;
    }

    /* MCQ/FAQ List Items */
    .widget-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    .widget-list-item {
        background: white;
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px dashed #ddd;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }

    .widget-list-item:hover {
        border-style: solid;
        border-color: var(--chatbot-primary);
        background: rgba(100, 50, 113, 0.05);
    }

    /* Feedback */
    .widget-feedback {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .feedback-chip {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        cursor: pointer;
        border: 1px solid #eee;
        background: white;
    }

    /* Product Cards */
    .widget-product-card {
        background: white;
        border-radius: 10px;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #eee;
        display: flex;
        gap: 10px;
        cursor: pointer;
    }

    .product-info-mini h6 {
        margin: 0;
        font-size: 14px;
        color: var(--chatbot-text-dark);
    }

    .product-price-mini {
        color: var(--chatbot-secondary);
        font-weight: 700;
        font-size: 13px;
    }

    /* Back Button */
    .widget-back-btn {
        font-size: 12px;
        color: #666;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 5px;
    }

    /* Mobile Overrides */
    @media (max-width: 480px) {
        .chatbot-window {
            width: calc(100% - 40px);
            right: 20px;
            bottom: 90px;
            height: 70vh;
        }
    }
</style>

<!-- Floating Button -->
<button class="chatbot-float-btn" id="chatbotToggle">
    <span class="chat-icon"><i class="fas fa-comment-dots"></i></span>
    <span class="close-icon"><i class="fas fa-times"></i></span>
</button>

<!-- Chat Window -->
<div class="chatbot-window" id="chatbotWindow">
    <div class="chatbot-widget-header">
        <div class="chatbot-widget-avatar">
            <img src="{{ asset('ecom_assets/images/chat-icon.png') }}" alt="Chatbot">
        </div>
        <div class="chatbot-widget-info">
            <h4>Lion Roaring Assistant</h4>
            <div class="chatbot-widget-status">
                <span class="chatbot-dot"></span>
                Online
            </div>
        </div>
    </div>

    <div class="chatbot-widget-messages" id="widgetMessages">
        <!-- Messages go here -->
    </div>

    <div class="chatbot-widget-footer">
        <input type="text" class="chatbot-widget-input" id="widgetInput" placeholder="Type your message...">
        <button class="chatbot-widget-send" id="widgetSend">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('chatbotToggle');
        const chatWindow = document.getElementById('chatbotWindow');
        const widgetInput = document.getElementById('widgetInput');
        const widgetSend = document.getElementById('widgetSend');
        const messagesArea = document.getElementById('widgetMessages');

        let isInitialized = false;
        let sessionId = localStorage.getItem('chatbot_session_id') || 'sess_' + Math.random().toString(36)
            .substr(2, 9);
        localStorage.setItem('chatbot_session_id', sessionId);

        let currentLanguage = 'en';
        let isAuthenticated = @json(Auth::check());
        let userName = @json(Auth::check() ? Auth::user()->name : null);
        let currentView = 'main';
        let faqCache = {};

        // Helper to get Google Translate language
        function getGoogleTranslateLang() {
            const cookie = document.cookie.split('; ').find(row => row.startsWith('googtrans='));
            if (cookie) {
                const parts = cookie.split('/');
                return parts[parts.length - 1];
            }
            return 'en';
        }

        

        currentLanguage = getGoogleTranslateLang();

        // Check window state from local storage
        if (localStorage.getItem('chatbot_open') === 'true') {
            chatWindow.classList.add('active');
            toggleBtn.classList.add('active');
            initChat();
            isInitialized = true;
        }

        // Toggle Chat Window
        toggleBtn.addEventListener('click', function() {
            const isActive = chatWindow.classList.toggle('active');
            toggleBtn.classList.toggle('active');
            localStorage.setItem('chatbot_open', isActive);

            if (isActive && !isInitialized) {
                initChat();
                isInitialized = true;
            }
        });

        // Send Message Event
        widgetSend.addEventListener('click', sendMessage);
        widgetInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        async function initChat() {
            try {
                const res = await fetch('{{ route('chatbot.init') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        language: currentLanguage
                    })
                });
                const data = await res.json();

                if (data.success) {
                    isAuthenticated = data.is_authenticated;
                    userName = data.user_name;

                    if (data.user_name) {
                        userName = data.user_name;
                        addBotMsg(`Welcome back, ${userName}! 👋 How can I help you today?`);
                        showMainMenu();
                    } else {
                        addBotMsg("Welcome to Lion Roaring! 👋");
                        addBotMsg("Before we begin, may I know your name?");
                        currentView = 'name_input';
                    }
                } else {
                    console.error(data.message);
                    addBotMsg("Sorry, I'm having trouble connecting. Please try again later.");
                }
            } catch (err) {
                console.error(err);
                addBotMsg("Sorry, I'm having trouble connecting. Please try again later.");
            }
        }

        async function sendMessage() {
            const text = widgetInput.value.trim();
            if (!text) return;

            addUserMsg(text);
            widgetInput.value = '';

            // Save user message to database
            saveMessage('user', text);

            if (currentView === 'name_input') {
                handleNameInput(text);
            } else if (currentView === 'estore_search') {
                searchProducts(text);
            } else if (currentView === 'elearning_search') {
                searchCourses(text);
            } else if (currentView === 'others') {
                searchKeywords(text);
            } else {
                searchKeywords(text);
            }
        }

        async function saveMessage(sender, msg, type = 'text', metadata = null) {
            try {
                await fetch('{{ route('chatbot.message') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        sender: sender,
                        message: msg,
                        message_type: type,
                        metadata: metadata
                    })
                });
            } catch (err) {
                console.error('Failed to save message:', err);
            }
        }

        async function handleNameInput(name) {
            try {
                const res = await fetch('{{ route('chatbot.guest-name') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        guest_name: name
                    })
                });
                const data = await res.json();
                if (data.success) {
                    userName = name;
                    addBotMsg(data.message);
                    currentView = 'main';
                    showMainMenu();
                }
            } catch (err) {
                console.error(err);
            }
        }

        function showMainMenu() {
            const options = [{
                    text: '🌐 Language',
                    action: 'lang'
                },
                {
                    text: '📦 Estore',
                    action: 'estore',
                    auth: true
                },
                {
                    text: '📚 E-learning',
                    action: 'elearning',
                    auth: true
                },
                {
                    text: '❓ FAQs',
                    action: 'faqs'
                },
                {
                    text: '💬 Others',
                    action: 'others'
                }
            ];

            const html = options
                .filter(o => !o.auth || isAuthenticated)
                .map(o =>
                    `<button class="widget-reply-btn" onclick="chatbotWidget.handleAction('${o.action}')">${o.text}</button>`
                )
                .join('');

            addQuickReplies(html);
        }

        // Exposed global for onclicks
        window.chatbotWidget = {
            handleAction: function(action) {
                if (action === 'lang') {
                    addBotMsg("Please select your language:");
                    fetchLanguages();
                } else if (action === 'estore') {
                    currentView = 'estore';
                    addBotMsg("Please enter your search query about e-store:");
                    currentView = 'estore_search';
                    addBackBtn();
                } else if (action === 'elearning') {
                    currentView = 'elearning';
                    addBotMsg("Please enter your search query about e-learning:");
                    currentView = 'elearning_search';
                    addBackBtn();
                } else if (action === 'faqs') {
                    fetchFaqs();
                } else if (action === 'others') {
                    currentView = 'others';
                    addBotMsg("Ask me anything about Lion Roaring:");
                    addBackBtn();
                }
            },
            setLang: async function(lang) {
                const res = await fetch('{{ route('chatbot.language') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        language: lang
                    })
                });
                const data = await res.json();
                if (data.success) {
                    console.log(data.success);

                    currentLanguage = lang;
                    addBotMsg(`Language updated! ✅`);

                    // Trigger Google Translate to match
                    if (window.forceSelectValue) {
                        const translateSelect = document.querySelector('.goog-te-combo');
                        if (translateSelect) {
                            window.forceSelectValue(translateSelect, lang);
                            console.log('first step');

                        }
                        console.log('not work first step');

                    } else if (window.changeGoogleTranslateLanguage) {
                        const translateSelect = document.querySelector('.goog-te-combo');
                        if (translateSelect) {
                            window.forceSelectValue(translateSelect, lang);
                            console.log('second step');

                        }
                         console.log('not work second step');
                    } else {
                        // Fallback manual trigger if helper not available
                        const translateSelect = document.querySelector('.goog-te-combo');
                        if (translateSelect) {
                            translateSelect.value = lang;
                            translateSelect.dispatchEvent(new Event('change'));
                            console.log('third step')
                        }
                         console.log('not work third step');
                    }

                    //  // Trigger Google Translate to match
                    // if (window.changeGoogleTranslateLanguage) {
                    //     window.changeGoogleTranslateLanguage(lang);
                    // } else {
                    //     console.error('Translation helper not found. Reloading...');
                    //     location.reload();
                    // }

                    showMainMenu();
                }
            },
            viewFaq: function(id) {
                fetchQuestions(id);
            },
            showAnsById: function(id) {
                const item = faqCache[id];
                if (item) {
                    this.showAns(item.id, item.question, item.answer);
                }
            },
            showAns: function(qId, qText, aText) {
                addUserMsg(qText);
                addBotMsg(aText);
                const html = `
                    <div class="widget-feedback">
                        <span class="feedback-chip" onclick="chatbotWidget.feedback(${qId}, true)">👍 Helpful</span>
                        <span class="feedback-chip" onclick="chatbotWidget.feedback(${qId}, false)">👎 No</span>
                    </div>
                `;
                addHTML(html);
                addBackBtn();
            },
            feedback: async function(id, ok) {
                await fetch('{{ route('chatbot.feedback') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        faq_question_id: id,
                        is_helpful: ok
                    })
                });
                addBotMsg("Thanks for feedback! 😊");
            },
            goHome: function() {
                currentView = 'main';
                addBotMsg("How else can I help?");
                showMainMenu();
            }
        };

        async function fetchLanguages() {
            try {
                const res = await fetch('{{ route('chatbot.languages') }}');
                const data = await res.json();
                if (data.success) {
                    const html = data.languages.map(l =>
                        `<button class="widget-reply-btn" onclick="chatbotWidget.setLang('${l.code}')">${l.name}</button>`
                    ).join('');
                    addQuickReplies(html);
                }
            } catch (err) {
                console.error(err);
                addBotMsg("Sorry, I couldn't load the languages.");
            }
        }

        async function fetchFaqs() {
            addBotMsg("Here are some frequently asked questions:");
            const res = await fetch('{{ route('chatbot.faq-questions') }}?session_id=' + sessionId);
            const data = await res.json();
            if (data.success) {
                if (data.questions.length === 0) {
                    addBotMsg("No FAQs available for your region yet.");
                } else {
                    let html = '<div class="widget-list">';
                    data.questions.forEach(q => {
                        faqCache[q.id] = q;
                        html +=
                            `<div class="widget-list-item" onclick="chatbotWidget.showAnsById(${q.id})">❓ ${q.question}</div>`;
                    });
                    html += '</div>';
                    addHTML(html);
                }
                addBackBtn();
            }
        }



        async function searchProducts(q) {
            showTyping();
            const res = await fetch(
                '{{ route('chatbot.search-estore') }}?query=' + encodeURIComponent(q) +
                '&session_id=' +
                sessionId);
            const data = await res.json();
            hideTyping();
            if (data.success && (data.products.length || data.response)) {
                if (data.response) {
                    addBotMsg(data.response);
                    saveMessage('bot', data.response);
                }

                if (data.products.length) {
                    data.products.forEach(p => {
                        const html = `
                            <div class="widget-product-card" onclick="window.location.href='{{ route('e-store.product-details', ['slug' => ':slug']) }}'.replace(':slug', '${p.slug}')">
                                <div class="product-info-mini">
                                    <h6>${p.name}</h6>
                                    <div class="product-price-mini">$${p.sale_price || p.price}</div>
                                </div>
                            </div>
                        `;
                        addHTML(html);
                    });
                }
            } else {
                addBotMsg("No products found for your search.");
            }
            addBackBtn();
        }

        async function searchCourses(q) {
            showTyping();
            const res = await fetch(
                '{{ route('chatbot.search-elearning') }}?query=' + encodeURIComponent(q) +
                '&session_id=' + sessionId);
            const data = await res.json();
            hideTyping();
            if (data.success && (data.courses.length || data.response)) {
                if (data.response) {
                    addBotMsg(data.response);
                    saveMessage('bot', data.response);
                }

                if (data.courses.length) {
                    data.courses.forEach(c => {
                        url = c.affiliate_link ?? '#';
                        const html = `
                            <div class="widget-product-card" onclick="window.location.href='${url}'">
                                <div class="product-info-mini">
                                    <h6>${c.name}</h6>
                                    <div class="product-price-mini">${c.price > 0 ? '$'+c.price : 'Free'}</div>
                                </div>
                            </div>
                        `;
                        addHTML(html);
                    });
                }
            } else {
                addBotMsg("No courses found.");
            }
            addBackBtn();
        }

        async function searchKeywords(q) {
            showTyping();
            const res = await fetch(
                '{{ route('chatbot.search-keywords') }}?query=' + encodeURIComponent(q) +
                '&session_id=' + sessionId);
            const data = await res.json();
            hideTyping();
            if (data.success) {
                if (data.response) {
                    addBotMsg(data.response);
                    saveMessage('bot', data.response);
                }

                if (data.products && data.products.length) {
                    addBotMsg("Found matching products in Estore:");
                    data.products.forEach(p => {
                        const html = `
                            <div class="widget-product-card" onclick="window.location.href='{{ route('e-store.product-details', ['slug' => ':slug']) }}'.replace(':slug', '${p.slug}')">
                                <div class="product-info-mini">
                                    <h6>${p.name}</h6>
                                    <div class="product-price-mini">$${p.sale_price || p.price}</div>
                                </div>
                            </div>
                        `;
                        addHTML(html);
                    });
                }

                if (data.courses && data.courses.length) {
                    addBotMsg("Found matching courses in E-learning:");
                    data.courses.forEach(c => {
                        url = c.affiliate_link ?? '#';
                        const html = `
                            <div class="widget-product-card" onclick="window.location.href='${url}'">
                                <div class="product-info-mini">
                                    <h6>${c.name}</h6>
                                    <div class="product-price-mini">${c.price > 0 ? '$'+c.price : 'Free'}</div>
                                </div>
                            </div>
                        `;
                        addHTML(html);
                    });
                }
            } else {
                addBotMsg(data.message);
                saveMessage('bot', data.message);
            }
            addBackBtn();
        }

        function addUserMsg(msg) {
            const div = document.createElement('div');
            div.className = 'chat-msg user';
            div.textContent = msg;
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function addBotMsg(msg) {
            const div = document.createElement('div');
            div.className = 'chat-msg bot';
            // If message contains HTML tags, just use innerHTML, otherwise replace newlines
            if (msg.includes('<') && msg.includes('>')) {
                div.innerHTML = msg;
            } else {
                div.innerHTML = msg.replace(/\n/g, '<br>');
            }
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function addHTML(html) {
            const div = document.createElement('div');
            div.className = 'chat-msg bot';
            div.innerHTML = html;
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function addQuickReplies(html) {
            const div = document.createElement('div');
            div.className = 'widget-quick-replies';
            div.innerHTML = html;
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function addBackBtn() {
            const div = document.createElement('div');
            div.innerHTML =
                `<div class="widget-back-btn" onclick="chatbotWidget.goHome()"><i class="fas fa-chevron-left"></i> Back to Menu</div>`;
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function showTyping() {
            const div = document.createElement('div');
            div.id = 'widgetTyping';
            div.className = 'chat-msg bot';
            div.innerHTML = '<i class="fas fa-ellipsis-h fa-beat"></i>';
            messagesArea.appendChild(div);
            scrollToBottom();
        }

        function hideTyping() {
            const el = document.getElementById('widgetTyping');
            if (el) el.remove();
        }

        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    });
</script>
