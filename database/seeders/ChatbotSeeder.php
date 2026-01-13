<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\ChatbotKeyword;

class ChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tables to avoid duplicates
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        ChatbotKeyword::truncate();
        // We probably shouldn't truncate Faq table as it might have real data,
        // but for seeding purpose let's at least ensure we have some data for common countries.
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Seed Keywords
        $keywords = [
            [
                'keyword' => 'working hours',
                'response' => 'Our support team is available from 9 AM to 6 PM, Monday to Saturday.',
            ],
            [
                'keyword' => 'contact',
                'response' => 'You can reach us at support@lionroaring.com or call us at +1-234-567-8900. Our team will respond within 24 hours.',
            ],
            [
                'keyword' => 'location',
                'response' => 'Our headquarters is located at 123 Main Street, City, Country. We also have regional offices worldwide.',
            ],
            [
                'keyword' => 'shipping cost',
                'response' => 'Shipping costs vary based on your location and the size of your order. Standard shipping is free for orders over $50.',
            ],
            [
                'keyword' => 'discount',
                'response' => 'We regularly offer discounts and promotions! Sign up for our newsletter to stay updated on the latest deals.',
            ],
        ];

        foreach ($keywords as $keywordData) {
            ChatbotKeyword::create($keywordData);
        }

        // Seed FAQs into the Faq model instead of ChatbotFaqQuestion
        $faqs = [
            [
                'country_code' => 'US',
                'question' => 'How do I create an account?',
                'answer' => 'To create an account, click on the "Sign Up" button in the top right corner of the page, fill in your details (name, email, password), and click "Register".',
            ],
            [
                'country_code' => 'US',
                'question' => 'How can I reset my password?',
                'answer' => 'Click on "Forgot Password" on the login page, enter your registered email address, and we will send you a link to reset your password.',
            ],
            [
                'country_code' => 'US',
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept credit cards (Visa, MasterCard, American Express), debit cards, PayPal, and bank transfers.',
            ],
            [
                'country_code' => 'GB',
                'question' => 'How do I track my order?',
                'answer' => 'After placing an order, you will receive a confirmation email with a tracking number. You can also track your order in "Order History".',
            ],
            [
                'country_code' => 'GB',
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for most items. Products must be unused and in original packaging.',
            ]
        ];

        foreach ($faqs as $faqData) {
            // Check if it already exists to avoid duplicates if we don't truncate
            if (!Faq::where('question', $faqData['question'])->where('country_code', $faqData['country_code'])->exists()) {
                Faq::create($faqData);
            }
        }
    }
}
