<?php

namespace Dzangolab\Auth;

use Carbon\Carbon;
use Dzangolab\Auth\Console\AddUserCommand;
use Dzangolab\Auth\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
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

        $access_token_lifetime = config('dzangolabAuth.access_token_lifetime.default');

        $refresh_token_lifetime = config('dzangolabAuth.refresh_token_lifetime.default');

        $app_routes_regex = '/(\/login|\/login\/refresh|\/signup)$/';

        $request_url = Request::url();

        if (preg_match($app_routes_regex, $request_url)) {
            $access_token_lifetime = config('dzangolabAuth.access_token_lifetime.app');

            $refresh_token_lifetime = config('dzangolabAuth.refresh_token_lifetime.app');
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

            $this->loadViewsFrom(__DIR__.'/../resources/views', 'dzangolab-auth');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/dzangolab-auth'),
            ]);

            $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dzangolab-auth');
        }

        // this is called from code for auto client creation
        $this->commands([
            ClientCommand::class,
        ]);

        /*// Api request service provider
        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(ApiRequest::class, function ($request, $app) {
            ApiRequest::createFrom($app['request'], $request);
        });*/
    }
}
