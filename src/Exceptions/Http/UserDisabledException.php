<?php

namespace Dzangolab\Auth\Exceptions\Http;

use Dzangolab\Auth\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserDisabledException extends AccessDeniedHttpException
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        parent::__construct(
            sprintf(
                'User `%s` is disabled.',
                $user->username
            ),
            null,
            AuthErrorCodes::USER_IS_DISABLED
        );
    }

    public function getUser()
    {
        return $this->user;
    }
}
