<?php

namespace Dzangolab\Auth\Http\Requests;

class CreateUserRequest extends ApiRequest
{
    public function rules()
    {
        return config()->has('dzangolabAuth.validation.create_user.rules')
            ? config('dzangolabAuth.validation.create_user.rules')
            : [];
    }
}
