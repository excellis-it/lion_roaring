<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chatbot conversations table
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('guest_name')->nullable();
            $table->string('language', 10)->default('en');
            $table->timestamps();

            $table->index(['session_id', 'user_id']);
        });

        // Chatbot messages table
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->enum('sender', ['user', 'bot']);
            $table->text('message');
            $table->string('message_type')->default('text'); // text, quick_reply, card
            $table->json('metadata')->nullable(); // For storing additional data like clicked options
            $table->timestamps();

            $table->index('conversation_id');
        });

        // Chatbot keywords (for "Others" section)
        Schema::create('chatbot_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->text('response');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index('keyword');
        });

        // Chatbot feedback table
        Schema::create('chatbot_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->foreignId('faq_id')->nullable()->constrained('faqs')->onDelete('set null');
            $table->boolean('is_helpful');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Chatbot analytics table
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->string('event_type'); // view, click, search, etc.
            $table->string('section')->nullable(); // estore, elearning, faq, etc.
            $table->json('event_data')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_analytics');
        Schema::dropIfExists('chatbot_feedback');
        Schema::dropIfExists('chatbot_keywords');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
    }
};
