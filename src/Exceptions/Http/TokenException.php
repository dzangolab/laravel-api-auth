<?php

namespace Dzangolab\Auth\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TokenException extends UnprocessableEntityHttpException
{
    public function __construct($message = 'Invalid token')
    {
        parent::__construct(
            $message,
            null,
            AuthErrorCodes::INVALID_TOKEN
        );
    }
}
