<?php

namespace Dzangolab\Auth\Providers;

use Dzangolab\Auth\Events\LoginEvent;
use Dzangolab\Auth\Events\PasswordChangedEvent;
use Dzangolab\Auth\Events\UserWasCreated;
use Dzangolab\Auth\Listeners\LoginListener;
use Dzangolab\Auth\Listeners\PasswordChangeListener;
use Dzangolab\Auth\Listeners\UserWasCreatedListener;
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
