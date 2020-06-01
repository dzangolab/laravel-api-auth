<?php

namespace Dzangolab\Auth\Exceptions;

use Exception;

class UserAlreadyExistsException extends Exception
{
    public function __construct($message = 'user already exists')
    {
        parent::__construct($message);
    }
}
