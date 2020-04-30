<?php

namespace Dzangolab\Auth\Http\Requests;

class ResetPasswordRequest extends ApiRequest
{
    public function rules()
    {
        return config()->has('dzangolabAuth.validation.reset_password.rules')
            ? config('dzangolabAuth.validation.reset_password.rules')
            : [];
    }
}
