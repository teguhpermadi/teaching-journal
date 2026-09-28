<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sanctum Token Prefix
    |--------------------------------------------------------------------------
    */
    'token_prefix' => 'sanctum',

    /*
    |--------------------------------------------------------------------------
    | Sanctum Authentication Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    */
    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'verify_csrf' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'encrypt_cookies' => EncryptCookies::class,
        'bind_session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'content_type' => \Illuminate\Routing\Middleware\TrimStrings::class,
        'accept' => \Illuminate\Routing\Middleware\ConvertEmptyStringsToNull::class,
    ],

];
