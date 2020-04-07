<?php

namespace Dzangolab\Auth\Exceptions\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenException extends HttpException
{
    public function __construct($message = 'Token error.')
    {
        parent::__construct(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $message
        );
    }
}
