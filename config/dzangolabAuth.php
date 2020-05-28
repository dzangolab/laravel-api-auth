<?php

return [
    /*
    | Lifetime of access token per client in seconds
    */
    'access_token_lifetime' => [
        'default' => env('AUTH_DEFAULT_ACCESS_TOKEN_LIFETIME', 600),
        'app' => env('AUTH_APP_ACCESS_TOKEN_LIFETIME', 600),
    ],

    'paths' => [
        'lang' => base_path('resources/lang/vendor/dzangolab-auth'),
        'views' => base_path('resources/views/vendor/dzangolab-auth'),
    ],

    /*
    | Lifetime of refresh token per client in seconds
    */
    'refresh_token_lifetime' => [
        'default' => env('AUTH_DEFAULT_REFRESH_TOKEN_LIFETIME', 864000),
        'app' => env('AUTH_APP_REFRESH_TOKEN_LIFETIME', 864000),
    ],

    /*
    | Send email with confirmation link to enable the user after signup
    */
    'user_confirmation' => env('AUTH_USER_CONFIRMATION', false),

    'username_same_as_email' => true,

    'validation' => [
        'change_password' => [
            'rules' => [
                'password' => 'array|required',
                'password.current_password' => 'required|string',
                'password.new_password' => 'required|string|min:6',
            ],
        ],
        'login' => [
            'rules' => [
                'username' => 'required',
                'password' => 'required',
            ],
        ],
        'reset_password' => [
            'rules' => [
                'password' => 'required|string|min:6',
            ],
        ],
        'reset_password_request' => [
            'rules' => [
                'email' => 'required|email|max:255',
            ],
        ],
        'create_user' => [
            'rules' => [
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:6',
            ],
        ],
        'update_user' => [
            'rules' => [
                'profile' => 'array',
                'profile.gender' => [
                    Illuminate\Validation\Rule::in([1, 2]),
                ],
                'profile.given_name' => 'string|min:2|max:255',
                'profile.surname' => 'string|min:2|max:255',
                'username' => 'string|min:3|max:255',
            ],
        ],
    ],
];
