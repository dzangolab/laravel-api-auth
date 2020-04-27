<?php

return [
    /*
    | Lifetime of access token per client in seconds
    */
    'access_token_lifetime' => [
        'default' => env('AUTH_DEFAULT_ACCESS_TOKEN_LIFETIME', 600),
        'app' => env('AUTH_APP_ACCESS_TOKEN_LIFETIME', 600),
    ],

    /*
    | Lifetime of refresh token per client in seconds
    */
    'refresh_token_lifetime' => [
        'default' => env('AUTH_DEFAULT_REFRESH_TOKEN_LIFETIME', 864000),
        'app' => env('AUTH_APP_REFRESH_TOKEN_LIFETIME', 864000),
    ],

    'password_clients' => [
        'app' => [
            'id' => env('CLIENT_PASSWORD_CLIENT_ID'),
            'secret' => env('CLIENT_PASSWORD_CLIENT_SECRET'),
        ],
    ],

    /*
    | Send email with confirmation link to enable the user after signup
    */
    'user_confirmation' => env('AUTH_USER_CONFIRMATION', false),

    'username_same_as_email' => true
];
