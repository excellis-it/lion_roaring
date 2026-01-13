<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'guest_name',
        'language',
    ];

    /**
     * Get the user that owns the conversation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for the conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    /**
     * Get the analytics for the conversation.
     */
    public function analytics()
    {
        return $this->hasMany(ChatbotAnalytics::class, 'conversation_id');
    }

    /**
     * Get the feedback for the conversation.
     */
    public function feedback()
    {
        return $this->hasMany(ChatbotFeedback::class, 'conversation_id');
    }

    /**
     * Check if conversation belongs to a logged-in user.
     */
    public function isAuthenticated()
    {
        return !is_null($this->user_id);
    }
}
