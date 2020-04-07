<?php

namespace Dzangolab\Auth\Exceptions;

use Dzangolab\Auth\Models\User;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserDisabledException extends BadRequestHttpException
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        parent::__construct(
            sprintf(
                'User "`%s` is disabled.',
                $user->username
            ),
            null,
            Response::HTTP_BAD_REQUEST
        );
    }

    public function getErrorCode()
    {
        return AuthErrorCodes::USER_IS_DISABLED;
    }

    public function getUser()
    {
        return $this->user;
    }
}
