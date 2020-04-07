<?php

namespace Dzangolab\Auth\Events;

use Dzangolab\Auth\Models\User;

class LoginEvent
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
