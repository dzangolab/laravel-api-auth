<?php

namespace Dzangolab\Auth\Exceptions;

class AuthErrorCodes
{
    const INVALID_CREDENTIALS = 969;

    const UNAUTHORIZED = 401;
    const USER_ALREADY_DISABLED = 996;
    const USER_ALREADY_ENABLED = 997;
    const USER_ALREADY_EXISTS = 998;
    const USER_IS_DISABLED = 995;
    const USER_NOT_FOUND = 999;
    const WRONG_PASSWORD = 968;
}
