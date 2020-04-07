<?php

namespace Dzangolab\Auth\Events;

use Dzangolab\Auth\Models\User;

abstract class AbstractUserEvent
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
