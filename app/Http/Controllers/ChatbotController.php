<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\ChatbotKeyword;
use App\Models\ChatbotAnalytics;
use App\Models\ChatbotFeedback;
use App\Models\Faq;
use App\Models\Country;
use App\Helpers\Helper;
use App\Models\Product;
use App\Models\ElearningProduct;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    /**
     * Initialize or retrieve conversation
     */
    public function initConversation(Request $request)
    {
        // try {
        $sessionId = $request->session_id ?? Str::uuid();

        $conversation = ChatbotConversation::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => Auth::id(),
                'language' => $request->language ?? 'en',
            ]
        );

        // Track analytics
        ChatbotAnalytics::create([
            'conversation_id' => $conversation->id,
            'event_type' => 'conversation_started',
            'section' => 'chatbot',
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'is_authenticated' => Auth::check(),
            'user_name' => Auth::check() ? Auth::user()->full_name : $conversation->guest_name,
        ]);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $th->getMessage(),
        //     ]);
        // }

    }

    /**
     * Update guest name
     */
    public function updateGuestName(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'guest_name' => 'required|string|max:255',
        ]);

        $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

        if ($conversation) {
            $conversation->update(['guest_name' => $request->guest_name]);

            $message = "Nice to meet you, {$request->guest_name}! 😊 How can I assist you today?";

            // Translate if needed
            if ($conversation->language !== 'en') {
                $message = $this->translateText($message, $conversation->language);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Change language
     */
    public function changeLanguage(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'language' => 'required|string|max:10',
        ]);

        $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

        if ($conversation) {
            $conversation->update(['language' => $request->language]);

            // Track analytics
            ChatbotAnalytics::create([
                'conversation_id' => $conversation->id,
                'event_type' => 'language_changed',
                'section' => 'settings',
                'event_data' => ['language' => $request->language],
            ]);

            return response()->json([
                'success' => true,
                'language' => $request->language,
            ]);
        }

        return response()->json(['success' => false], 404);
    }



    /**
     * Get FAQ questions (filtered by country)
     */
    public function getFaqQuestions(Request $request)
    {
        $countryCode = Helper::getVisitorCountryCode();

        $questions = Faq::where('country_code', $countryCode)
            ->orderBy('id', 'asc')
            ->get();

        // Translate FAQs if needed
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();
            if ($conversation && $conversation->language !== 'en') {
                foreach ($questions as $q) {
                    $q->question = $this->translateText($q->question, $conversation->language);
                    $q->answer = $this->translateText($q->answer, $conversation->language);
                }
            }

            // Track analytics
            if ($conversation) {
                ChatbotAnalytics::create([
                    'conversation_id' => $conversation->id,
                    'event_type' => 'faqs_viewed',
                    'section' => 'faq',
                    'event_data' => ['country_code' => $countryCode],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }

    /**
     * Get languages for the current country
     */
    public function getLanguages()
    {
        $countryCode = Helper::getVisitorCountryCode();
        $country = Country::with('languages')->where('code', $countryCode)->first();

        $languages = [];
        if ($country && $country->languages) {
            foreach ($country->languages as $lang) {
                $languages[] = [
                    'code' => $lang->code,
                    'name' => $lang->name,
                ];
            }
        }

        // If no languages found, default to English
        if (empty($languages)) {
            $languages[] = ['code' => 'en', 'name' => 'English'];
        }

        return response()->json([
            'success' => true,
            'languages' => $languages,
        ]);
    }

    /**
     * Search products in Estore
     */
    public function searchEstoreProducts(Request $request)
    {
        $query = $request->get('query');

        // Translate query to English if needed
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();
            if ($conversation && $conversation->language !== 'en') {
                $query = $this->translateText($query, 'en');
            }
        }

        $keyword = ChatbotKeyword::findByKeyword($query);

        $products = Product::where('is_deleted', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'slug', 'price', 'sale_price']);

        // Track search
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

            // Translate if needed
            if ($conversation && $conversation->language !== 'en') {
                foreach ($products as $product) {
                    $product->name = $this->translateText($product->name, $conversation->language);
                }
            }

            if ($conversation) {
                ChatbotAnalytics::create([
                    'conversation_id' => $conversation->id,
                    'event_type' => 'product_search',
                    'section' => 'estore',
                    'event_data' => [
                        'query' => $query,
                        'results_count' => $products->count(),
                        'found_keyword' => $keyword ? true : false
                    ],
                ]);
            }
        }

        if ($keyword) {
            $keyword->incrementUsage();
        }

        return response()->json([
            'success' => true,
            'products' => $products,
            'response' => $keyword ? ($conversation && $conversation->language !== 'en' ? $this->translateText($keyword->response, $conversation->language) : $keyword->response) : null,
        ]);
    }

    /**
     * Search courses in E-learning
     */
    public function searchElearningCourses(Request $request)
    {
        $query = $request->get('query');

        // Translate query to English if needed
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();
            if ($conversation && $conversation->language !== 'en') {
                $query = $this->translateText($query, 'en');
            }
        }

        $keyword = ChatbotKeyword::findByKeyword($query);

        $courses = ElearningProduct::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })
            ->limit(5)
            ->get(['id', 'name', 'slug', 'price', 'affiliate_link']);

        // Track search
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

            // Translate if needed
            if ($conversation && $conversation->language !== 'en') {
                foreach ($courses as $course) {
                    $course->name = $this->translateText($course->name, $conversation->language);
                }
            }

            if ($conversation) {
                ChatbotAnalytics::create([
                    'conversation_id' => $conversation->id,
                    'event_type' => 'course_search',
                    'section' => 'elearning',
                    'event_data' => [
                        'query' => $query,
                        'results_count' => $courses->count(),
                        'found_keyword' => $keyword ? true : false
                    ],
                ]);
            }
        }

        if ($keyword) {
            $keyword->incrementUsage();
        }

        return response()->json([
            'success' => true,
            'courses' => $courses,
            'response' => $keyword ? ($conversation && $conversation->language !== 'en' ? $this->translateText($keyword->response, $conversation->language) : $keyword->response) : null,
        ]);
    }

    /**
     * Search keywords for "Others" section
     */
    public function searchKeywords(Request $request)
    {
        $query = $request->get('query');

        // Translate query to English if needed
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();
            if ($conversation && $conversation->language !== 'en') {
                $query = $this->translateText($query, 'en');
            }
        }

        $keyword = ChatbotKeyword::findByKeyword($query);

        $products = collect();
        $courses = collect();
        $response = null;

        // If a keyword is found, respect its search_type
        if ($keyword) {
            $keyword->incrementUsage();
            $response = $keyword->response;

            if ($keyword->search_type == 'estore') {
                $products = Product::where('is_deleted', false)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->limit(5)
                    ->get(['id', 'name', 'slug', 'price', 'sale_price']);
            } elseif ($keyword->search_type == 'elearning') {
                $courses = ElearningProduct::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                    ->limit(5)
                    ->get(['id', 'name', 'slug', 'price', 'affiliate_link']);
            } else {
                // 'others' - show response and also general search results
                $products = Product::where('is_deleted', false)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->limit(3)
                    ->get(['id', 'name', 'slug', 'price', 'sale_price']);

                $courses = ElearningProduct::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                    ->limit(3)
                    ->get(['id', 'name', 'slug', 'price', 'affiliate_link']);
            }
        } else {
            // No keyword found, do a general search
            $products = Product::where('is_deleted', false)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(3)
                ->get(['id', 'name', 'slug', 'price', 'sale_price']);

            $courses = ElearningProduct::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
                ->limit(3)
                ->get(['id', 'name', 'slug', 'price', 'affiliate_link']);
        }

        // Track search & Translate
        $conversation = null;
        if ($request->session_id) {
            $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

            if ($conversation) {
                // Translate results if needed
                if ($conversation->language !== 'en') {
                    foreach ($products as $product) {
                        $product->name = $this->translateText($product->name, $conversation->language);
                    }
                    foreach ($courses as $course) {
                        $course->name = $this->translateText($course->name, $conversation->language);
                    }
                    if ($response) {
                        $response = $this->translateText($response, $conversation->language);
                    }
                }

                ChatbotAnalytics::create([
                    'conversation_id' => $conversation->id,
                    'event_type' => 'keyword_search',
                    'section' => $keyword->search_type ?? 'others',
                    'event_data' => [
                        'query' => $query,
                        'found_keyword' => $keyword ? true : false,
                        'products_count' => $products->count(),
                        'courses_count' => $courses->count()
                    ],
                ]);
            }
        }

        if ($response || $products->isNotEmpty() || $courses->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'response' => $response,
                'products' => $products,
                'courses' => $courses,
            ]);
        }

        $msg = "Sorry, I couldn't find anything matching your query. Please rephrase or contact support.";
        if ($conversation && $conversation->language !== 'en') {
            $msg = $this->translateText($msg, $conversation->language);
        }

        return response()->json([
            'success' => false,
            'message' => $msg,
        ]);
    }

    /**
     * Submit feedback
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'is_helpful' => 'required|boolean',
            'faq_question_id' => 'nullable|exists:chatbot_faq_questions,id',
            'comment' => 'nullable|string',
        ]);

        $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

        if ($conversation) {
            ChatbotFeedback::create([
                'conversation_id' => $conversation->id,
                'faq_id' => $request->faq_question_id, // We keep the request param name for compatibility or change it in JS
                'is_helpful' => $request->is_helpful,
                'comment' => $request->comment,
            ]);

            // Update FAQ question counts - Skip for Faq model as it doesn't have these fields
            /*
            if ($request->faq_question_id) {
                $question = ChatbotFaqQuestion::find($request->faq_question_id);
                if ($question) {
                    if ($request->is_helpful) {
                        $question->markHelpful();
                    } else {
                        $question->markNotHelpful();
                    }
                }
            }
            */

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Save message
     */
    public function saveMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'sender' => 'required|in:user,bot',
            'message' => 'required|string',
            'message_type' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $conversation = ChatbotConversation::where('session_id', $request->session_id)->first();

        if ($conversation) {
            ChatbotMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => $request->sender,
                'message' => $request->message,
                'message_type' => $request->message_type ?? 'text',
                'metadata' => $request->metadata,
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(Request $request)
    {
        $conversation = ChatbotConversation::where('session_id', $request->session_id)
            ->with('messages')
            ->first();

        if ($conversation) {
            return response()->json([
                'success' => true,
                'messages' => $conversation->messages,
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Helper to translate text using Google Translate free API (gtx)
     */
    private function translateText($text, $targetLang)
    {
        if ($targetLang === 'en' || empty($text)) {
            return $text;
        }

        try {
            $client = new Client();
            $response = $client->get('https://translate.googleapis.com/translate_a/single', [
                'query' => [
                    'client' => 'gtx',
                    'sl' => 'auto',
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            // Extract translated text from the response structure
            if (isset($result[0])) {
                $translatedText = '';
                foreach ($result[0] as $part) {
                    if (isset($part[0])) {
                        $translatedText .= $part[0];
                    }
                }
                return $translatedText ?: $text;
            }

            return $text;
        } catch (\Exception $e) {
            // In case of error, return original text
            return $text;
        }
    }
}
