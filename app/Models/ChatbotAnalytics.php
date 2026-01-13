<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'event_type',
        'section',
        'event_data',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    /**
     * Get the conversation that owns the analytics.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
