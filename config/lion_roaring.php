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

];
