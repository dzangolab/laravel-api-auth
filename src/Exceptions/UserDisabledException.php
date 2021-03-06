<?php

namespace Dzangolab\Auth\Exceptions;

use Dzangolab\Auth\Models\User;
use Exception;

class UserDisabledException extends Exception
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        parent::__construct(
            sprintf(
                'User `%s` is disabled.',
                $user->username
            )
        );
    }

    public function getUser()
    {
        return $this->user;
    }
}
