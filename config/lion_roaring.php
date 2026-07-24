<?php

return [

    /*
    |--------------------------------------------------------------------------
    | In-app member plan (Flutter app only)
    |--------------------------------------------------------------------------
    |
    | When true, the Flutter app shows member-plan UI, checks, and redirects for
    | MEMBER_SOVEREIGN users (exposed via profile API as in_app_membership).
    |
    | When false, the mobile app hides those features and skips renewal gates.
    | The web user panel is unaffected and always uses membership for sovereigns.
    |
    */
    'in_app_membership' => filter_var(env('IN_APP_MEMBERSHIP', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Chatbot mode (web + Flutter)
    |--------------------------------------------------------------------------
    |
    | AI → external RAG / mobile WebView URL. NORMAL → built-in chat assistant.
    |
    */
    'chatbot' => strtoupper(trim(preg_replace('/\s*#.*$/', '', (string) env('CHATBOT', 'NORMAL')) ?: 'NORMAL')),

    /*
    |--------------------------------------------------------------------------
    | Mobile chatbot WebView URL (Flutter, when chatbot=AI)
    |--------------------------------------------------------------------------
    */
    'mobile_chatbot_url' => (string) env(
        'MOBILE_CHATBOT_URL',
        'https://chatbot.lionroaring.us/chat?botId=6a47b3eef6cdf474ed1a9145&token=0c098bd4e7495141105cc679566e13b3c46579a80be443e72e3e1b3696b21d7e'
    ),

    /*
    |--------------------------------------------------------------------------
    | Registration tier lock (global domain — cheapest paid tier)
    |--------------------------------------------------------------------------
    |
    | Web registration always enforces this rule.
    |
    | Mobile API clients must send X-App-Build >= this value (or the feature
    | flag in X-Lion-Client-Features) before the API returns is_locked or
    | rejects a locked tier. Older store builds without those headers keep
    | the previous behaviour.
    |
    */
    'membership_tier_lock_min_app_build' => (int) env('MEMBERSHIP_TIER_LOCK_MIN_APP_BUILD', 39),
    'membership_tier_lock_feature' => 'membership_tier_lock',

];
