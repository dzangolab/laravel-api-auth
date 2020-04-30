<?php

namespace Dzangolab\Auth\Http\Requests;

class LoginRequest extends ApiRequest
{
    public function rules()
    {

        return config()->has('dzangolabAuth.validation.login.rules')
            ? config('dzangolabAuth.validation.login.rules')
            : [];
    }
}
