<?php

namespace Dzangolab\Auth\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('user not found', null, AuthErrorCodes::USER_NOT_FOUND);
    }
}
