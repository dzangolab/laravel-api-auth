<?php

namespace Dzangolab\Auth\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('User not found', null, AuthErrorCodes::USER_NOT_FOUND);
    }
}
