<?php

namespace Dzangolab\Auth;

use Carbon\Carbon;
use Dzangolab\Auth\Console\AddUserCommand;
use Dzangolab\Auth\Events\LoginEvent;
use Dzangolab\Auth\Events\PasswordChangedEvent;
use Dzangolab\Auth\Events\UserWasCreated;
use Dzangolab\Auth\Http\Requests\ApiRequest;
use Dzangolab\Auth\Listeners\LoginListener;
use Dzangolab\Auth\Listeners\PasswordChangeListener;
use Dzangolab\Auth\Listeners\UserWasCreatedListener;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $listen = [
        LoginEvent::class => [
            LoginListener::class,
        ],
        PasswordChangedEvent::class => [
            PasswordChangeListener::class,
        ],
        UserWasCreated::class => [
            UserWasCreatedListener::class,
        ],
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::routes(
            function ($router) {
                $router->forAccessTokens();
                // Uncomment for allowing personal access tokens
                // $router->forPersonalAccessTokens();
                $router->forTransientTokens();
            }
        );

        $access_token_lifetime = config('auth.access_token_lifetime.default');

        $refresh_token_lifetime = config('auth.refresh_token_lifetime.default');

        $app_routes_regex = '/(\/login|\/login\/refresh|\/signup)$/';

        $request_url = Request::url();

        if (preg_match($app_routes_regex, $request_url)) {
            $access_token_lifetime = config('auth.access_token_lifetime.app');

            $refresh_token_lifetime = config('auth.refresh_token_lifetime.app');
        }

        Passport::tokensExpireIn(
            Carbon::now()->addSeconds($access_token_lifetime)
        );

        Passport::refreshTokensExpireIn(
            Carbon::now()->addSeconds($refresh_token_lifetime)
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'auth-migrations');

            $this->publishes([
                __DIR__.'/../database/factories' => database_path('factories'),
            ], 'auth-factories');

            $this->publishes([
                __DIR__.'/../config/dzangolabAuth.php' => config_path('dzangolabAuth.php'),
            ], 'auth-config');

            $this->commands([
                AddUserCommand::class,
            ]);
        }

        /*// Api request service provider
        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(ApiRequest::class, function ($request, $app) {
            ApiRequest::createFrom($app['request'], $request);
        });*/
    }
}