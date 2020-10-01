# Laravel API auth

## Introduction

Laravel auth package to be used for API.

## Requirement:

- Laravel framework `^8.6.0`
- Laravel Passport `^10.0.1`

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
                "url": "https://github.com/dzangolab/laravel-api-auth.git"
            }
        ],
        "require": {
            ...
            "dzangolab/laravel-api-auth": "^0.1.0"
        }
     ```
1. `php artisan vendor:publish --provider="Dzangolab\Auth\AuthServiceProvider"`
1. in `app/Http/Kernel.php` use `use Dzangolab\Auth\Middleware\AccessTokenChecker;`
1. [TODO UKS 2020-04-28, this step really needed? check.] in `app/Http/Middleware/EncryptCookies.php` use AuthUserService from `Dzangolab\Auth\Service`
1. Passport is fully configured by the package so you just need to do `php artisan passport:install`
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
1. Run `php artisan migrate` to create tables.
1. Now you can use the auth and api middleware as `Route::middleware(['auth:api'])` to define routes.

### Add `code` to exception response

Override `convertExceptionToArray` method in `app/Exception/Handler.php` and add code to the array.

```php
<?php

namespace App\Exceptions;

use Illuminate\Support\Arr;

class Handler extends ExceptionHandler
{
    protected function convertExceptionToArray(Throwable $e)
    {
        return config('app.debug') ? [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'code' => $e->getCode(),
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }
}
```
