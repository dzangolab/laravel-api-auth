<?php

namespace Dzangolab\Auth\Http\Requests;

class ResetEmailRequest extends ApiRequest
{
    const CONFIG_VALIDATION_RULES = 'dzangolabAuth.validation.reset_password_request.rules';
}
