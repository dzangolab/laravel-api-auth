<?php

namespace Dzangolab\Auth\Http\Requests;

class UpdatePasswordRequest extends ApiRequest
{
    public function rules()
    {
        return config()->has('dzangolabAuth.validation.change_password.rules')
            ? config('dzangolabAuth.validation.change_password.rules')
            : [];
    }
}
