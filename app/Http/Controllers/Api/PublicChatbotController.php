<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotKeyword;
use App\Models\ElearningProduct;
use App\Models\Faq;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PublicChatbotController extends Controller
{
    public function query(Request $request)
    {
        $query = trim((string) $request->input('query'));
        $language = $this->resolveLanguage($request);
        $searchQuery = $language !== 'en'
            ? $this->translateText($query, 'en', $language)
            : $query;
        $keyword = ChatbotKeyword::findByKeyword($searchQuery);

        $products = collect();
        $courses = collect();
        $answer = null;
        $intent = 'others';

        if ($keyword) {
            $answer = $keyword->response;
            $intent = $keyword->search_type ?: 'others';
            $keyword->incrementUsage();
        }

        if (!$keyword || $intent === 'others' || $intent === 'estore') {
            $products = $this->searchProductsQuery($searchQuery, $intent === 'estore' ? 5 : 3);
        }

        if (!$keyword || $intent === 'others' || $intent === 'elearning') {
            $courses = $this->searchCoursesQuery($searchQuery, $intent === 'elearning' ? 5 : 3);
        }

        if ($language !== 'en') {
            if ($answer) {
                $answer = $this->translateText($answer, $language, 'en');
            }
            foreach ($products as $product) {
                $product->name = $this->translateText((string) $product->name, $language, 'en');
            }
            foreach ($courses as $course) {
                $course->name = $this->translateText((string) $course->name, $language, 'en');
            }
        }

        if (!$answer && $products->isEmpty() && $courses->isEmpty()) {
            return response()->json([
                'success' => false,
                'answer' => $language !== 'en'
                    ? $this->translateText("Sorry, I couldn't find anything matching your query.", $language, 'en')
                    : "Sorry, I couldn't find anything matching your query.",
                'intent' => 'none',
                'products' => [],
                'courses' => [],
                'suggestions' => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'answer' => $answer,
            'intent' => $intent,
            'products' => $products->values(),
            'courses' => $courses->values(),
            'suggestions' => [],
        ]);
    }

    public function faqs(Request $request)
    {
        $countryCode = $request->query('country_code');
        $language = $this->resolveLanguage($request);
        $query = Faq::query();
        if ($countryCode) {
            $query->where('country_code', $countryCode);
        }

        $faqs = $query
            ->orderBy('id', 'asc')
            ->get(['id', 'country_code', 'question', 'answer']);

        if ($language !== 'en') {
            foreach ($faqs as $faq) {
                $faq->question = $this->translateText((string) $faq->question, $language, 'en');
                $faq->answer = $this->translateText((string) $faq->answer, $language, 'en');
            }
        }

        return response()->json([
            'success' => true,
            'faqs' => $faqs,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $language = $this->resolveLanguage($request);
        $query = (string) $request->query('query', '');
        $searchQuery = $language !== 'en'
            ? $this->translateText($query, 'en', $language)
            : $query;
        $products = $this->searchProductsQuery($searchQuery, 10);
        if ($language !== 'en') {
            foreach ($products as $product) {
                $product->name = $this->translateText((string) $product->name, $language, 'en');
            }
        }
        return response()->json([
            'success' => true,
            'products' => $products->values(),
        ]);
    }

    public function searchCourses(Request $request)
    {
        $language = $this->resolveLanguage($request);
        $query = (string) $request->query('query', '');
        $searchQuery = $language !== 'en'
            ? $this->translateText($query, 'en', $language)
            : $query;
        $courses = $this->searchCoursesQuery($searchQuery, 10);
        if ($language !== 'en') {
            foreach ($courses as $course) {
                $course->name = $this->translateText((string) $course->name, $language, 'en');
            }
        }
        return response()->json([
            'success' => true,
            'courses' => $courses->values(),
        ]);
    }

    private function searchProductsQuery(string $query, int $limit = 5)
    {
        return Product::query()
            ->where('is_deleted', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'price', 'sale_price']);
    }

    private function searchCoursesQuery(string $query, int $limit = 5)
    {
        return ElearningProduct::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'price', 'affiliate_link']);
    }

    private function resolveLanguage(Request $request): string
    {
        $language = strtolower((string) $request->input('language', $request->query('language', '')));
        if ($language !== '') {
            return $language;
        }

        $sessionId = (string) $request->input('session_id', $request->query('session_id', ''));
        if ($sessionId !== '') {
            $conversation = ChatbotConversation::where('session_id', $sessionId)->first();
            if ($conversation && !empty($conversation->language)) {
                return strtolower((string) $conversation->language);
            }
        }

        return 'en';
    }

    private function translateText($text, $targetLang, $sourceLang = 'auto')
    {
        if (empty($text) || $targetLang === $sourceLang || ($targetLang === 'en' && $sourceLang === 'en')) {
            return $text;
        }

        try {
            $client = new Client();
            $response = $client->get('https://translate.googleapis.com/translate_a/single', [
                'query' => [
                    'client' => 'gtx',
                    'sl' => $sourceLang,
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text
                ]
            ]);

            $result = json_decode($response->getBody(), true);
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
            return $text;
        }
    }
}
