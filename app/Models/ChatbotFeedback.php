<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'faq_id',
        'is_helpful',
        'comment',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    /**
     * Get the conversation that owns the feedback.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    /**
     * Get the FAQ that owns the feedback.
     */
    public function faq()
    {
        return $this->belongsTo(Faq::class, 'faq_id');
    }
}
