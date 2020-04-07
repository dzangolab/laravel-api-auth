<?php

namespace Dzangolab\Auth\Listeners;

use Dzangolab\Auth\Events\LoginEvent;
use Dzangolab\Auth\Models\User;
use Illuminate\Support\Facades\Log;

class LoginListener
{
    protected $authUserRepository;

    public function __construct(User $authUserRepository)
    {
        $this->authUserRepository = $authUserRepository;
    }

    public function handle(LoginEvent $event)
    {
        Log::info('User id of user who successfully logged in: '.$event->user->id);

        $this->getAuthUserRepository()
            ->updateLastLogin($event->user);
    }

    protected function getAuthUserRepository(): User
    {
        return $this->authUserRepository;
    }
}
