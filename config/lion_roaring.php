<?php

return [

    /*
    |--------------------------------------------------------------------------
    | In-app member plan (mobile)
    |--------------------------------------------------------------------------
    |
    | When true, the Flutter app shows the member plan screen in the sidebar,
    | dashboard summary, and profile section.
    |
    | When false, those UI entry points are hidden and membership plan checks /
    | forced renewal redirects are disabled (web MemberAccess + mobile app).
    |
    */
    'in_app_membership' => filter_var(env('IN_APP_MEMBERSHIP', true), FILTER_VALIDATE_BOOLEAN),

];
