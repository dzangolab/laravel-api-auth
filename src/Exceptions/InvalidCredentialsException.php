<?php

namespace Dzangolab\Auth\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class InvalidCredentialsException extends UnauthorizedHttpException
{
    public function __construct($message = 'Invalid credentials.', Exception $previous = null)
    {
        parent::__construct(
            '',
            $message,
            $previous,
            AuthErrorCodes::INVALID_CREDENTIALS
        );
    }
}
