<?php

namespace Dzangolab\Auth\Http\Requests;

class ResetEmailRequest extends ApiRequest
{
    public function rules()
    {
        return config()->has('dzangolabAuth.validation.reset_password_request.rules')
            ? config('dzangolabAuth.validation.reset_password_request.rules')
            : [];
    }
}
