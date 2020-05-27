<?php

namespace Dzangolab\Auth\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class AuthEventServiceProvider extends EventServiceProvider
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
}
