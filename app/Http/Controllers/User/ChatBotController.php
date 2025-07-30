<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class ChatBotController extends Controller
{
    //
    // public function FaqChat(Request $request)
    // {
    //     $message = $request->message;
    //     $faq = Faq::where('question', 'like', '%' . $message . '%')->orWhere('answer', 'like', '%' . $message . '%')->first();
    //     if ($faq) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => $faq->answer
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Sorry, I do not understand your query.'
    //         ], 201);
    //     }
    // }

    // AI ChatBot
    // public function FaqChat(Request $request)
    // {
    //     try {
    //         $message = strtolower(trim($request->message));

    //         // Retrieve all FAQs
    //         $faqs = Faq::all();

    //         $bestMatch = null;
    //         $bestScore = 0;

    //         foreach ($faqs as $faq) {
    //             $question = strtolower($faq->question);
    //             $answer = strtolower($faq->answer);

    //             // Check for direct match
    //             if (str_contains($question, $message) || str_contains($answer, $message)) {
    //                 return response()->json([
    //                     'status' => 'success',
    //                     'message' => $faq->answer
    //                 ], 200);
    //             }

    //             // Calculate similarity score
    //             similar_text($message, $question, $questionScore);
    //             similar_text($message, $answer, $answerScore);

    //             $maxScore = max($questionScore, $answerScore);

    //             if ($maxScore > $bestScore) {
    //                 $bestScore = $maxScore;
    //                 $bestMatch = $faq;
    //             }
    //         }

    //         // Define a threshold for similarity (e.g., 40%)
    //         if ($bestMatch && $bestScore > 40) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => $bestMatch->answer
    //             ], 200);
    //         }

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Sorry, I do not understand your query.'
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Something went wrong. Please try again later.'
    //         ], 201);
    //     }
    // }


    public function FaqChat(Request $request)
    {
        try {
            $message = strtolower(trim($request->message));

            // Remove stopwords and preprocess query
            $processedMessage = $this->preprocessText($message);

            // Retrieve all FAQs
            $faqs = Faq::all();

            if ($faqs->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sorry, no FAQs available at the moment.'
                ], 201);
            }

            $bestMatch = null;
            $bestScore = 0;

            foreach ($faqs as $faq) {
                $question = strtolower($faq->question);
                $answer = strtolower($faq->answer);

                // Preprocess FAQ question
                $processedQuestion = $this->preprocessText($question);

                // Direct Match (Highest Priority)
                if (str_contains($processedQuestion, $processedMessage)) {
                    return response()->json([
                        'status' => 'success',
                        'message' => $faq->answer
                    ], 200);
                }

                // Calculate similarity scores
                $levenshteinScore = $this->levenshteinSimilarity($processedMessage, $processedQuestion);
                $jaccardScore = $this->jaccardSimilarity($processedMessage, $processedQuestion);
                $tfidfScore = $this->tfidfSimilarity($processedMessage, $processedQuestion);
                $keywordScore = $this->keywordMatch($processedMessage, $processedQuestion);

                // Weighted Score Calculation
                $finalScore = ($levenshteinScore * 0.4) + ($jaccardScore * 0.3) + ($tfidfScore * 0.2) + ($keywordScore * 0.1);

                if ($finalScore > $bestScore) {
                    $bestScore = $finalScore;
                    $bestMatch = $faq;
                }
            }

            $content = htmlspecialchars_decode($bestMatch->answer);

            if ($bestMatch && $bestScore > 10) {  // Adjusted threshold for better accuracy
                return response()->json([
                    'status' => 'success',
                    'message' => $content
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, I do not understand your query. Can you please rephrase it? <br>Or Contact Us at <a href="' . env('APP_URL') . '/contact-us">' . env('APP_URL') . '/contact-us</a>',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 201);
        }
    }

    /**
     * Preprocess text: Lowercase, remove stopwords, lemmatization
     */
    private function preprocessText($text)
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Define stopwords to remove
        $stopwords = ['how', 'to', 'my', 'is', 'do', 'can', 'i', 'the', 'a', 'an'];

        // Remove punctuation
        $text = preg_replace('/[^\w\s]/', '', $text);

        // Tokenize
        $words = explode(' ', $text);

        // Remove stopwords & apply stemming
        $filteredWords = array_diff($words, $stopwords);

        // Join back into a string
        return implode(' ', $filteredWords);
    }

    /**
     * Calculate Levenshtein similarity
     */
    private function levenshteinSimilarity($str1, $str2)
    {
        $distance = levenshtein($str1, $str2);
        $maxLength = max(strlen($str1), strlen($str2));

        if ($maxLength == 0) return 100;

        return (1 - ($distance / $maxLength)) * 100;
    }

    /**
     * Calculate Jaccard similarity
     */
    private function jaccardSimilarity($str1, $str2)
    {
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        if (count($union) == 0) return 0;

        return (count($intersection) / count($union)) * 100;
    }

    /**
     * Calculate TF-IDF similarity
     */
    private function tfidfSimilarity($str1, $str2)
    {
        $words1 = array_count_values(explode(' ', $str1));
        $words2 = array_count_values(explode(' ', $str2));

        $intersection = array_intersect_key($words1, $words2);
        $dotProduct = array_sum(array_map(fn($x, $y) => $x * $y, $intersection, array_intersect_key($words2, $intersection)));

        $magnitude1 = sqrt(array_sum(array_map(fn($x) => $x ** 2, $words1)));
        $magnitude2 = sqrt(array_sum(array_map(fn($x) => $x ** 2, $words2)));

        if ($magnitude1 * $magnitude2 == 0) return 0;

        return ($dotProduct / ($magnitude1 * $magnitude2)) * 100;
    }

    /**
     * Smart Keyword Matching
     */
    private function keywordMatch($message, $question)
    {
        $keywords = [
            // 🚛 Tracking & Shipping
            'track' => ['track', 'shipment', 'order', 'package', 'delivery', 'courier', 'status', 'dispatch', 'logistics', 'tracking ID', 'parcel', 'shipping', 'expected arrival', 'carrier', 'waybill', 'ETA', 'real-time tracking', 'transport', 'in transit', 'tracking number'],

            // 💰 Refund & Returns
            'refund' => ['refund', 'money back', 'return', 'policy', 'warranty', 'exchange', 'claim', 'reimbursement', 'cancellation', 'credit', 'replacement', 'return period', 'damaged item', 'wrong order', 'compensation', 'restocking fee', 'store credit', 'chargeback', 'customer rights', 'dispute'],

            // 📞 Customer Support
            'customer' => ['customer', 'support', 'help', 'service', 'assistance', 'care', 'contact', 'helpline', 'technical support', 'complaint', 'resolution', 'user query', 'ticket', 'live chat', 'phone support', 'email support', '24/7 service', 'response time', 'customer satisfaction', 'feedback'],

            // 🎁 Discounts & Offers
            'discount' => ['discount', 'offer', 'promo', 'sale', 'voucher', 'coupon', 'cashback', 'limited-time', 'clearance', 'rebate', 'special deal', 'seasonal sale', 'price drop', 'membership discount', 'holiday sale', 'black friday', 'bundle offer', 'exclusive deal', 'flash sale', 'student discount'],

            // 💳 Payment & Checkout
            'payment' => ['payment', 'checkout', 'transaction', 'billing', 'invoice', 'receipt', 'credit card', 'debit card', 'paypal', 'net banking', 'cash on delivery', 'EMI', 'wire transfer', 'wallet', 'auto debit', 'UPI', 'cryptocurrency', 'payment gateway', 'failed payment', 'secure payment'],

            // 🔐 Security & Privacy
            'security' => ['security', 'encryption', 'firewall', 'antivirus', 'data protection', 'privacy', 'SSL', 'cybersecurity', 'authentication', 'malware', 'ransomware', 'phishing', 'spam', 'identity theft', 'two-factor authentication', 'secure browsing', 'password', 'safety', 'hacking', 'fraud'],

            // 👤 User Account Management
            'account' => ['account', 'profile', 'registration', 'signup', 'login', 'logout', 'password reset', 'user ID', 'authentication', 'account settings', 'subscription', 'preferences', 'linked accounts', 'profile update', 'access', 'email verification', 'account security', 'deactivate', 'manage account', 'membership'],

            // 🏷️ Product & Shopping
            'product' => ['product', 'item', 'goods', 'merchandise', 'brand', 'new arrival', 'bestseller', 'review', 'rating', 'quality', 'warranty', 'features', 'description', 'specifications', 'availability', 'comparison', 'variant', 'limited edition', 'bulk purchase', 'gift'],

            // 🚚 Delivery Services
            'delivery' => ['delivery', 'shipping', 'courier', 'home delivery', 'express', 'same-day', 'scheduled delivery', 'tracking', 'ETA', 'international shipping', 'logistics', 'carrier', 'package', 'drop-off', 'freight', 'dispatch', 'customs', 'shipping cost', 'fast shipping', 'pickup'],

            // 📜 Terms & Conditions
            'terms' => ['terms', 'conditions', 'policy', 'agreement', 'terms of service', 'contract', 'compliance', 'legal', 'guidelines', 'usage policy', 'disclaimer', 'privacy policy', 'liability', 'warranty terms', 'customer rights', 'refund policy', 'return policy', 'TOS', 'regulations', 'binding agreement'],

            // 🔍 SEO & Web Search
            'seo' => ['SEO', 'search engine', 'ranking', 'keywords', 'backlinks', 'on-page optimization', 'off-page optimization', 'meta tags', 'site speed', 'user experience', 'mobile-friendly', 'domain authority', 'traffic', 'page rank', 'algorithm update', 'content strategy', 'SERP', 'organic search', 'Google Analytics', 'CTR'],

            // 📊 Analytics & Data
            'analytics' => ['analytics', 'data', 'metrics', 'tracking', 'performance', 'insights', 'traffic analysis', 'A/B testing', 'conversion rate', 'bounce rate', 'click-through rate', 'heatmaps', 'user behavior', 'real-time data', 'KPIs', 'funnel analysis', 'website analytics', 'customer analytics', 'predictive analysis', 'Google Analytics'],

            // 💻 Technology & Software
            'technology' => ['technology', 'AI', 'machine learning', 'IoT', 'blockchain', 'automation', 'software', 'hardware', 'mobile', 'cloud computing', '5G', 'robotics', 'programming', 'data science', 'virtual reality', 'cybersecurity', 'networking', 'innovation', 'tech industry', 'gadgets'],

            // 🏢 Business & Entrepreneurship
            'business' => ['business', 'entrepreneur', 'startup', 'corporate', 'enterprise', 'market', 'finance', 'growth', 'business plan', 'investment', 'funding', 'strategy', 'operations', 'management', 'networking', 'B2B', 'B2C', 'partnership', 'small business', 'innovation'],

            // 📢 Marketing & Branding
            'marketing' => ['marketing', 'advertising', 'branding', 'digital marketing', 'social media', 'PPC', 'email marketing', 'influencer marketing', 'affiliate marketing', 'strategy', 'target audience', 'promotion', 'campaign', 'lead generation', 'conversion', 'retargeting', 'content marketing', 'SEO marketing', 'paid ads', 'analytics'],

            // 🏪 E-commerce & Online Shopping
            'ecommerce' => ['ecommerce', 'online shopping', 'store', 'checkout', 'cart', 'products', 'payment gateway', 'merchant', 'dropshipping', 'inventory', 'customer support', 'conversion', 'digital store', 'POS', 'shipping', 'tax', 'shopping experience', 'user reviews', 'gift cards', 'loyalty points'],

            // 🏦 Finance & Investment
            'finance' => ['finance', 'investment', 'stock market', 'trading', 'cryptocurrency', 'mutual funds', 'forex', 'savings', 'banking', 'credit score', 'loans', 'mortgage', 'budgeting', 'wealth management', 'tax planning', 'insurance', 'interest rates', 'financial planning', 'debt management', 'personal finance']
        ];


        foreach ($keywords as $category => $synonyms) {
            foreach ($synonyms as $synonym) {
                if (str_contains($message, $synonym) && str_contains($question, $category)) {
                    return 100;
                }
            }
        }

        return 0;
    }






    //
}
