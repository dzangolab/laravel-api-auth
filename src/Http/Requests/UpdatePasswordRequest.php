<?php

namespace Dzangolab\Auth\Http\Requests;

class UpdatePasswordRequest extends ApiRequest
{
    const CONFIG_VALIDATION_RULES = 'dzangolabAuth.validation.change_password.rules';
}
