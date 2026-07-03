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
