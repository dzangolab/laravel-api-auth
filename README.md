# Laravel API auth

## Introduction

Laravel auth package to be used for API.

### Installation

1. Add package to composer.json and do `composer update`
    ```php
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/dzangolab/laravel-api-consumer.git"
            },
            {
                "type": "git",
                "url": "https://github.com/dzangolab/laravel-api-consumer.git"
            }
        ],
        "require": {
            ...
            "dzangolab/laravel-api-auth": "^0.1.0"
        }
     ```
1. `php artisan vendor:publish --provider="Dzangolab\Auth\AuthServiceProvider"`
1. add providers to `config/app.php`.
    ```php
    Dzangolab\Auth\AuthServiceProvider::class,
    Dzangolab\Auth\Providers\AuthRouteServiceProvider::class,
    ```
1. in `app/Http/Kernel.php` use `use Dzangolab\Auth\Middleware\AccessTokenChecker;`
1. [TODO UKS 2020-04-28, this step really needed? check.] in `app/Http/Middleware/EncryptCookies.php` use AuthUserService from `Dzangolab\Auth\Service`
1. Passport is fully configured by the package so you just need to do `php artisan --force passport:keys` to generate passport keys
1. Use `php artisan route:list` to see new routes from Dzabgolab\Auth namespace.
1. But not ready yet. You haven't configured your laravel authentication yet. In `auth.php`, update guards:
    ```php
    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    'providers' => [
        'users' => [
            ...
            'model' => Dzangolab\Auth\Models\User::class,
        ]
    ]
    ```
1. Now you can use the auth and api middleware as `Route::middleware(['auth:api'])` to define routes.
