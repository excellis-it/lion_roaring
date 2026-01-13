<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #643271;
            --secondary-color: #d98b1c;
            --text-dark: #1a1a1a;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Chatbot Container */
        .chatbot-container {
            width: 100%;
            max-width: 420px;
            height: 650px;
            background: var(--bg-white);
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .chatbot-header {
            background: linear-gradient(135deg, var(--primary-color), #7b3a8a);
            color: white;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 6px rgba(100, 50, 113, 0.2);
        }

        .chatbot-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), #e6a342);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .chatbot-info h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .chatbot-status {
            font-size: 13px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Messages Container */
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: var(--bg-light);
            scroll-behavior: smooth;
        }

        .chatbot-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chatbot-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chatbot-messages::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        /* Message */
        .message {
            margin-bottom: 16px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-content {
            display: inline-block;
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .message.bot .message-content {
            background: white;
            color: var(--text-dark);
            border-bottom-left-radius: 4px;
            box-shadow: var(--shadow-sm);
        }

        .message.user {
            text-align: right;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, var(--primary-color), #7b3a8a);
            color: white;
            border-bottom-right-radius: 4px;
        }

        /* Quick Reply Buttons */
        .quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            animation: fadeIn 0.4s ease-out;
        }

        .quick-reply-btn {
            padding: 10px 16px;
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .quick-reply-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .quick-reply-btn.active {
            background: var(--primary-color);
            color: white;
        }

        /* List Items (FAQ, Products) */
        .chat-list {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .chat-list-item {
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .chat-list-item:hover {
            border-color: var(--primary-color);
            transform: translateX(4px);
            box-shadow: var(--shadow-md);
        }

        .chat-list-item-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
            font-size: 14px;
        }

        .chat-list-item-desc {
            font-size: 13px;
            color: var(--text-light);
        }

        /* Input Container */
        .chatbot-input-container {
            padding: 16px 20px;
            background: white;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .chatbot-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 24px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .chatbot-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(100, 50, 113, 0.1);
        }

        .chatbot-send-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary-color), #7b3a8a);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-md);
        }

        .chatbot-send-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .chatbot-send-btn:active {
            transform: scale(0.95);
        }

        /* Language Selector */
        .language-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
        }

        .language-modal.active {
            display: flex;
        }

        .language-modal-content {
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.3s ease-out;
        }

        .language-modal-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .language-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .language-option {
            padding: 16px;
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .language-option:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-2px);
        }

        .language-option.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 20px;
            color: var(--text-dark);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 12px;
        }

        .back-button:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateX(-4px);
        }

        /* Typing Indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: white;
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            width: fit-content;
            box-shadow: var(--shadow-sm);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.7;
            }

            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        /* Feedback Buttons */
        .feedback-buttons {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .feedback-btn {
            padding: 6px 12px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .feedback-btn:hover {
            background: var(--bg-light);
            border-color: var(--primary-color);
        }

        .feedback-btn.helpful:hover {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }

        .feedback-btn.not-helpful:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        /* Product Card */
        .product-card {
            background: white;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-top: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .product-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .product-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .product-price {
            color: var(--secondary-color);
            font-weight: 700;
            font-size: 16px;
        }

        .product-price.sale {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .original-price {
            text-decoration: line-through;
            color: var(--text-light);
            font-size: 14px;
            font-weight: 400;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .chatbot-container {
                max-width: 100%;
                height: 100vh;
                border-radius: 0;
            }

            .language-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Hidden class */
        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="chatbot-container">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-avatar">🤖</div>
            <div class="chatbot-info">
                <h2>AI Assistant</h2>
                <div class="chatbot-status">
                    <span class="status-dot"></span>
                    <span>Online</span>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="chatbot-messages" id="chatMessages">
            <!-- Messages will be dynamically inserted here -->
        </div>

        <!-- Input -->
        <div class="chatbot-input-container">
            <input type="text" class="chatbot-input" id="chatInput" placeholder="Type a message...">
            <button class="chatbot-send-btn" id="sendBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Language Modal -->
    <div class="language-modal" id="languageModal">
        <div class="language-modal-content">
            <div class="language-modal-header">Select Language</div>
            <div class="language-grid">
                <div class="language-option" data-lang="en">🇬🇧 English</div>
                <div class="language-option" data-lang="hi">🇮🇳 Hindi</div>
                <div class="language-option" data-lang="ta">தமிழ் Tamil</div>
                <div class="language-option" data-lang="te">తెలుగు Telugu</div>
                <div class="language-option" data-lang="bn">বাংলা Bengali</div>
                <div class="language-option" data-lang="ml">മലയാളം Malayalam</div>
                <div class="language-option" data-lang="kn">ಕನ್ನಡ Kannada</div>
                <div class="language-option" data-lang="mr">मराठी Marathi</div>
                <div class="language-option" data-lang="ur">اردو Urdu</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Chatbot JavaScript
        const chatbot = {
            sessionId: null,
            currentLanguage: 'en',
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
            userName: {{ Auth::check() ? "'" . Auth::user()->name . "'" : 'null' }},
            currentView: 'main',

            init() {
                this.sessionId = this.generateSessionId();
                this.setupEventListeners();
                this.initConversation();
            },

            generateSessionId() {
                return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            },

            setupEventListeners() {
                // Send button
                document.getElementById('sendBtn').addEventListener('click', () => this.sendMessage());

                // Enter key
                document.getElementById('chatInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendMessage();
                });

                // Language options
                document.querySelectorAll('.language-option').forEach(option => {
                    option.addEventListener('click', (e) => this.selectLanguage(e.target.dataset.lang));
                });

                // Close language modal on outside click
                document.getElementById('languageModal').addEventListener('click', (e) => {
                    if (e.target.id === 'languageModal') {
                        this.closeLanguageModal();
                    }
                });
            },

            async initConversation() {
                try {
                    const response = await axios.post('/chatbot/init', {
                        session_id: this.sessionId,
                        language: this.currentLanguage
                    });

                    if (response.data.success) {
                        this.isAuthenticated = response.data.is_authenticated;
                        this.userName = response.data.user_name;
                        this.showWelcomeMessage();
                    }
                } catch (error) {
                    console.error('Error initializing conversation:', error);
                    this.addBotMessage("Sorry, I'm having trouble connecting. Please refresh the page.");
                }
            },

            showWelcomeMessage() {
                if (this.isAuthenticated) {
                    this.addBotMessage(`Welcome back, ${this.userName}! 👋 How can I help you today?`);
                } else {
                    this.addBotMessage("Hi there! 👋 What's your name?");
                    this.currentView = 'name_input';
                }

                setTimeout(() => this.showMainMenu(), 500);
            },

            async sendMessage() {
                const input = document.getElementById('chatInput');
                const message = input.value.trim();

                if (!message) return;

                this.addUserMessage(message);
                input.value = '';

                // Handle different views
                if (this.currentView === 'name_input' && !this.isAuthenticated) {
                    await this.setGuestName(message);
                } else if (this.currentView === 'estore_search') {
                    await this.searchEstoreProducts(message);
                } else if (this.currentView === 'elearning_search') {
                    await this.searchElearningCourses(message);
                } else if (this.currentView === 'others') {
                    await this.searchKeywords(message);
                }
            },

            async setGuestName(name) {
                try {
                    const response = await axios.post('/chatbot/guest-name', {
                        session_id: this.sessionId,
                        guest_name: name
                    });

                    if (response.data.success) {
                        this.userName = name;
                        this.addBotMessage(response.data.message);
                        this.currentView = 'main';
                    }
                } catch (error) {
                    console.error('Error setting guest name:', error);
                }
            },

            showMainMenu() {
                const options = [{
                        text: '🌐 Change Language',
                        action: 'change_language'
                    },
                    {
                        text: '📦 Estore Questions',
                        action: 'estore',
                        requiresAuth: true
                    },
                    {
                        text: '📚 E-learning Questions',
                        action: 'elearning',
                        requiresAuth: true
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

                const buttons = options
                    .filter(opt => !opt.requiresAuth || this.isAuthenticated)
                    .map(opt => {
                        if (opt.requiresAuth && !this.isAuthenticated) {
                            return '';
                        }
                        return `<button class="quick-reply-btn" onclick="chatbot.handleMenuOption('${opt.action}')">${opt.text}</button>`;
                    })
                    .join('');

                this.addQuickReplies(buttons);
            },

            handleMenuOption(option) {
                switch (option) {
                    case 'change_language':
                        this.openLanguageModal();
                        break;
                    case 'estore':
                        if (!this.isAuthenticated) {
                            this.addBotMessage("Please log in to access the Estore section.");
                            return;
                        }
                        this.showEstoreMenu();
                        break;
                    case 'elearning':
                        if (!this.isAuthenticated) {
                            this.addBotMessage("Please log in to access the E-learning section.");
                            return;
                        }
                        this.showElearningMenu();
                        break;
                    case 'faqs':
                        this.showFaqCategories();
                        break;
                    case 'others':
                        this.showOthersPrompt();
                        break;
                }
            },

            openLanguageModal() {
                document.getElementById('languageModal').classList.add('active');
            },

            closeLanguageModal() {
                document.getElementById('languageModal').classList.remove('active');
            },

            async selectLanguage(lang) {
                try {
                    const response = await axios.post('/chatbot/language', {
                        session_id: this.sessionId,
                        language: lang
                    });

                    if (response.data.success) {
                        this.currentLanguage = lang;
                        this.closeLanguageModal();

                        const langNames = {
                            en: 'English',
                            hi: 'Hindi',
                            ta: 'Tamil',
                            te: 'Telugu',
                            bn: 'Bengali',
                            ml: 'Malayalam',
                            kn: 'Kannada',
                            mr: 'Marathi',
                            ur: 'Urdu'
                        };

                        this.addBotMessage(`Language changed to ${langNames[lang]} ✅`);
                        this.showBackButton();
                    }
                } catch (error) {
                    console.error('Error changing language:', error);
                }
            },

            showEstoreMenu() {
                this.currentView = 'estore';
                this.addBotMessage("What would you like to do in the Estore?");

                const options = `
                    <button class="quick-reply-btn" onclick="chatbot.handleEstoreOption('search')">🔍 Search Product</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleEstoreOption('orders')">📦 Order Status</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleEstoreOption('payments')">💳 Payments & Refunds</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleEstoreOption('shipping')">🚚 Shipping Information</button>
                `;

                this.addQuickReplies(options);
                this.showBackButton();
            },

            handleEstoreOption(option) {
                switch (option) {
                    case 'search':
                        this.currentView = 'estore_search';
                        this.addBotMessage("Please enter the product name you're looking for:");
                        break;
                    case 'orders':
                        this.addBotMessage(
                            "You can check your order status by logging into your account and visiting the Orders page."
                            );
                        this.showBackButton();
                        break;
                    case 'payments':
                        this.addBotMessage(
                            "For payment and refund inquiries, please visit our Payments section or contact support."
                            );
                        this.showBackButton();
                        break;
                    case 'shipping':
                        this.addBotMessage(
                            "Standard shipping takes 5-7 business days. Express shipping is available for 2-3 business days delivery."
                            );
                        this.showBackButton();
                        break;
                }
            },

            async searchEstoreProducts(query) {
                this.showTypingIndicator();

                try {
                    const response = await axios.get('/chatbot/search-estore', {
                        params: {
                            query,
                            session_id: this.sessionId
                        }
                    });

                    this.removeTypingIndicator();

                    if (response.data.success && response.data.products.length > 0) {
                        this.addBotMessage("Here are the products I found:");

                        response.data.products.forEach(product => {
                            const priceHtml = product.sale_price ?
                                `<div class="product-price sale"><span>$${product.sale_price}</span><span class="original-price">$${product.price}</span></div>` :
                                `<div class="product-price">$${product.price}</div>`;

                            const productHtml = `
                                <div class="product-card" onclick="window.location.href='/estore/products/${product.slug}'">
                                    <div class="product-name">${product.name}</div>
                                    ${priceHtml}
                                </div>
                            `;
                            this.addBotHTML(productHtml);
                        });
                    } else {
                        this.addBotMessage("Sorry, I couldn't find any products matching your search.");
                    }

                    this.showBackButton();
                    this.currentView = 'estore';
                } catch (error) {
                    this.removeTypingIndicator();
                    console.error('Error searching products:', error);
                    this.addBotMessage("Sorry, I encountered an error while searching for products.");
                }
            },

            showElearningMenu() {
                this.currentView = 'elearning';
                this.addBotMessage("What would you like to do in E-learning?");

                const options = `
                    <button class="quick-reply-btn" onclick="chatbot.handleElearningOption('browse')">📘 Browse Courses</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleElearningOption('enrolled')">🎓 My Enrolled Courses</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleElearningOption('search')">🔍 Search Courses</button>
                    <button class="quick-reply-btn" onclick="chatbot.handleElearningOption('certificates')">📜 Certificates</button>
                `;

                this.addQuickReplies(options);
                this.showBackButton();
            },

            handleElearningOption(option) {
                switch (option) {
                    case 'browse':
                        this.addBotMessage("You can browse all courses by visiting our E-learning catalog.");
                        this.showBackButton();
                        break;
                    case 'enrolled':
                        this.addBotMessage("Check your enrolled courses in your dashboard.");
                        this.showBackButton();
                        break;
                    case 'search':
                        this.currentView = 'elearning_search';
                        this.addBotMessage("Please enter the course name you're looking for:");
                        break;
                    case 'certificates':
                        this.addBotMessage("You can download your certificates from your profile page.");
                        this.showBackButton();
                        break;
                }
            },

            async searchElearningCourses(query) {
                this.showTypingIndicator();

                try {
                    const response = await axios.get('/chatbot/search-elearning', {
                        params: {
                            query,
                            session_id: this.sessionId
                        }
                    });

                    this.removeTypingIndicator();

                    if (response.data.success && response.data.courses.length > 0) {
                        this.addBotMessage("Here are the courses I found:");

                        response.data.courses.forEach(course => {
                            const courseHtml = `
                                <div class="product-card" onclick="window.location.href='/elearning/course/${course.slug}'">
                                    <div class="product-name">${course.name}</div>
                                    <div class="product-price">${course.price > 0 ? '$' + course.price : 'Free'}</div>
                                </div>
                            `;
                            this.addBotHTML(courseHtml);
                        });
                    } else {
                        this.addBotMessage("Sorry, I couldn't find any courses matching your search.");
                    }

                    this.showBackButton();
                    this.currentView = 'elearning';
                } catch (error) {
                    this.removeTypingIndicator();
                    console.error('Error searching courses:', error);
                    this.addBotMessage("Sorry, I encountered an error while searching for courses.");
                }
            },

            async showFaqCategories() {
                this.showTypingIndicator();

                try {
                    const response = await axios.get('/chatbot/faq-categories');
                    this.removeTypingIndicator();

                    if (response.data.success && response.data.categories.length > 0) {
                        this.currentView = 'faq_categories';
                        this.addBotMessage("Please select a category:");

                        const categories = response.data.categories.map(cat => `
                            <div class="chat-list-item" onclick="chatbot.showFaqQuestions(${cat.id})">
                                <div class="chat-list-item-title">${cat.icon} ${cat.name}</div>
                                <div class="chat-list-item-desc">${cat.active_questions_count || 0} questions</div>
                            </div>
                        `).join('');

                        this.addBotHTML(`<div class="chat-list">${categories}</div>`);
                        this.showBackButton();
                    }
                } catch (error) {
                    this.removeTypingIndicator();
                    console.error('Error fetching FAQ categories:', error);
                }
            },

            async showFaqQuestions(categoryId) {
                this.showTypingIndicator();

                try {
                    const response = await axios.get(`/chatbot/faq-questions/${categoryId}`, {
                        params: {
                            session_id: this.sessionId
                        }
                    });

                    this.removeTypingIndicator();

                    if (response.data.success && response.data.questions.length > 0) {
                        this.currentView = 'faq_questions';
                        this.addBotMessage("Here are the questions:");

                        const questions = response.data.questions.map(q => `
                            <div class="chat-list-item" onclick="chatbot.showFaqAnswer(${q.id}, '${q.question.replace(/'/g, "\\'")}', '${q.answer.replace(/'/g, "\\'")}')">
                                <div class="chat-list-item-title">${q.question}</div>
                            </div>
                        `).join('');

                        this.addBotHTML(`<div class="chat-list">${questions}</div>`);
                        this.showBackButton();
                    }
                } catch (error) {
                    this.removeTypingIndicator();
                    console.error('Error fetching FAQ questions:', error);
                }
            },

            showFaqAnswer(questionId, question, answer) {
                this.addUserMessage(question);
                this.addBotMessage(answer);

                const feedbackHtml = `
                    <div class="feedback-buttons">
                        <button class="feedback-btn helpful" onclick="chatbot.submitFeedback(${questionId}, true)">👍 Helpful</button>
                        <button class="feedback-btn not-helpful" onclick="chatbot.submitFeedback(${questionId}, false)">👎 Not Helpful</button>
                    </div>
                `;
                this.addBotHTML(feedbackHtml);
                this.showBackButton();
            },

            async submitFeedback(questionId, isHelpful) {
                try {
                    await axios.post('/chatbot/feedback', {
                        session_id: this.sessionId,
                        faq_question_id: questionId,
                        is_helpful: isHelpful
                    });

                    this.addBotMessage("Thank you for your feedback! 😊");
                } catch (error) {
                    console.error('Error submitting feedback:', error);
                }
            },

            showOthersPrompt() {
                this.currentView = 'others';
                this.addBotMessage("Please type your question, and I'll try to help you:");
                this.showBackButton();
            },

            async searchKeywords(query) {
                this.showTypingIndicator();

                try {
                    const response = await axios.get('/chatbot/search-keywords', {
                        params: {
                            query,
                            session_id: this.sessionId
                        }
                    });

                    this.removeTypingIndicator();

                    if (response.data.success) {
                        this.addBotMessage(response.data.response);
                    } else {
                        this.addBotMessage(response.data.message);
                    }

                    this.showBackButton();
                } catch (error) {
                    this.removeTypingIndicator();
                    console.error('Error searching keywords:', error);
                    this.addBotMessage("Sorry, I couldn't find an answer for that. Please contact support.");
                }
            },

            showBackButton() {
                const backHtml =
                    `<button class="back-button" onclick="chatbot.goBackToMainMenu()">← Back to Main Menu</button>`;
                this.addBotHTML(backHtml);
            },

            goBackToMainMenu() {
                this.currentView = 'main';
                this.addBotMessage("How can I help you?");
                this.showMainMenu();
            },

            addUserMessage(message) {
                const messagesContainer = document.getElementById('chatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user';
                messageDiv.innerHTML = `<div class="message-content">${this.escapeHtml(message)}</div>`;
                messagesContainer.appendChild(messageDiv);
                this.scrollToBottom();
            },

            addBotMessage(message) {
                const messagesContainer = document.getElementById('chatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot';
                messageDiv.innerHTML = `<div class="message-content">${this.escapeHtml(message)}</div>`;
                messagesContainer.appendChild(messageDiv);
                this.scrollToBottom();
            },

            addBotHTML(html) {
                const messagesContainer = document.getElementById('chatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot';
                messageDiv.innerHTML = html;
                messagesContainer.appendChild(messageDiv);
                this.scrollToBottom();
            },

            addQuickReplies(buttonsHtml) {
                const messagesContainer = document.getElementById('chatMessages');
                const repliesDiv = document.createElement('div');
                repliesDiv.className = 'quick-replies';
                repliesDiv.innerHTML = buttonsHtml;
                messagesContainer.appendChild(repliesDiv);
                this.scrollToBottom();
            },

            showTypingIndicator() {
                const messagesContainer = document.getElementById('chatMessages');
                const typingDiv = document.createElement('div');
                typingDiv.className = 'message bot typing-indicator-message';
                typingDiv.innerHTML = `
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                `;
                messagesContainer.appendChild(typingDiv);
                this.scrollToBottom();
            },

            removeTypingIndicator() {
                const typingIndicator = document.querySelector('.typing-indicator-message');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            },

            scrollToBottom() {
                const messagesContainer = document.getElementById('chatMessages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            },

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };

        // Initialize chatbot when page loads
        document.addEventListener('DOMContentLoaded', () => {
            chatbot.init();
        });
    </script>
</body>

</html>
