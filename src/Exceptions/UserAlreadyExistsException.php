<?php

namespace Dzangolab\Auth\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserAlreadyExistsException extends UnprocessableEntityHttpException
{
    public function __construct($message = 'user already exists')
    {
        parent::__construct(
            $message,
            null,
            AuthErrorCodes::USER_ALREADY_EXISTS
        );
    }
}
