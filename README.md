# Laravel API auth

## Introduction

Laravel auth package to be used for API.

### Installation

1. Add package to composer.json and do composer update
1. `php artisan vendor:publish --provider="Dzangolab\Auth\AuthServiceProvider"`
1. add providers to `config/app.php`.
    ```
    Dzangolab\Auth\AuthServiceProvider::class,
    Dzangolab\Auth\Providers\AuthRouteServiceProvider::class,
    ```
1. in `app/Http/Kernel.php` use `use Dzangolab\Auth\Middleware\AccessTokenChecker;`
1. in `app/Http/Middleware/EncryptCookies.php` use AuthUserService from `Dzangolab\Auth\Service`
